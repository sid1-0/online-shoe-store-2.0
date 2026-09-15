<?php
/**
 * AssociationMiner - cross-category association rule recommendations.
 *
 * Policy:
 *   shoes           => socks + shoe_care
 *   socks           => shoes
 *   shoe_care => shoes
 */
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Product.php';

class AssociationMiner
{
    private $db;
    private $productModel;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->productModel = new Product();
    }

    /**
     * @return array{title: string, recommendations: array}
     */
    public function getRecommendations($shoeId, $limit = 4)
    {
        $allMap = $this->productModel->getAllMappedById();
        if (!isset($allMap[$shoeId])) {
            return ['title' => 'Frequently Bought Together', 'recommendations' => []];
        }

        $current = $allMap[$shoeId];
        $currentCategory = strtolower(trim($current['category'] ?? 'shoes'));
        if ($currentCategory === 'accessories') {
            $currentCategory = 'socks';
        }

        if ($currentCategory === 'socks' || $currentCategory === 'shoe_care') {
            $targetCategories = ['shoes'];
            $title = ($currentCategory === 'socks')
                ? 'Suggested Shoes to Pair With These Socks'
                : 'Suggested Shoes for This Cleaner';
        } else {
            $targetCategories = ['socks', 'shoe_care'];
            $title = 'Frequently Bought Together — Socks & Shoe Care';
            $currentCategory = 'shoes';
        }

        $baskets = $this->loadBaskets();
        $totalBaskets = count($baskets);
        $basketsWithCurrent = 0;
        $coOccurrence = [];
        $shoeBasketCount = [];

        foreach ($baskets as $items) {
            $hasCurrent = isset($items[$shoeId]);
            if ($hasCurrent) {
                $basketsWithCurrent++;
            }
            foreach (array_keys($items) as $otherId) {
                if ($otherId == $shoeId) {
                    continue;
                }
                $shoeBasketCount[$otherId] = ($shoeBasketCount[$otherId] ?? 0) + 1;
                if ($hasCurrent) {
                    $coOccurrence[$otherId] = ($coOccurrence[$otherId] ?? 0) + 1;
                }
            }
        }

        $supportA = ($totalBaskets > 0 && $basketsWithCurrent > 0)
            ? ($basketsWithCurrent / $totalBaskets)
            : 0.05;
        $currentPrice = (float) ($current['price'] ?? 1.0);
        $brand = $current['brand'] ?? '';
        $scored = [];

        foreach ($coOccurrence as $otherId => $count) {
            $other = $allMap[$otherId] ?? [];
            if (!$this->isValidTarget($other['category'] ?? '', $targetCategories)) {
                continue;
            }

            $otherPrice = (float) ($other['price'] ?? 1.0);
            $maxP = max(1.0, max($currentPrice, $otherPrice));
            $priceSim = max(0.15, 1.0 - (abs($currentPrice - $otherPrice) / $maxP));

            $supportAB = $count / max(1, $totalBaskets);
            $supportB = ($shoeBasketCount[$otherId] ?? 1) / max(1, $totalBaskets);
            $confidence = $count / max(1, $basketsWithCurrent);
            $lift = ($supportA * $supportB) > 0 ? ($supportAB / ($supportA * $supportB)) : 1.0;
            $leverage = $supportAB - ($supportA * $supportB);
            $denom = 1.0 - $confidence;
            $conviction = ($denom > 0.0001) ? (1.0 - $supportB) / $denom : 5.0;
            $conviction = min(10.0, max(0.0, $conviction));

            $sameBrand = isset($other['brand']) && strcasecmp($brand, $other['brand']) === 0;
            $brandBonus = $sameBrand ? 1.25 : 1.0;
            $score = (($lift * 0.35) + ($confidence * 0.25) + ($priceSim * 0.25) + (max(0, $leverage) * 0.15))
                * 1.6 * $brandBonus;

            $scored[$otherId] = [
                'confidence' => $confidence,
                'lift' => $lift,
                'leverage' => $leverage,
                'conviction' => $conviction,
                'price_sim' => $priceSim,
                'category' => strtolower($other['category'] ?? 'shoes'),
                'score' => $score + 100.0,
            ];
        }

        // Attribute fallback for sparse purchase data
        if (count($scored) < $limit) {
            foreach ($allMap as $otherId => $other) {
                if ($otherId == $shoeId || isset($scored[$otherId])) {
                    continue;
                }
                if (!$this->isValidTarget($other['category'] ?? '', $targetCategories)) {
                    continue;
                }

                $otherPrice = (float) $other['price'];
                $maxP = max(1.0, max($currentPrice, $otherPrice));
                $priceSim = max(0.15, 1.0 - (abs($currentPrice - $otherPrice) / $maxP));
                $sameBrand = strcasecmp($brand, $other['brand'] ?? '') === 0;
                $brandConf = $sameBrand ? 0.90 : 0.55;
                $attrConfidence = ($brandConf * 0.50) + ($priceSim * 0.50);
                $attrLift = 1.0 + ($attrConfidence * 1.5);
                $attrLeverage = $attrConfidence * 0.05;
                $attrConviction = 1.0 + $attrConfidence;
                $brandBonus = $sameBrand ? 1.25 : 1.0;
                $score = (($attrLift * 0.35) + ($attrConfidence * 0.25) + ($priceSim * 0.25) + ($attrLeverage * 0.15))
                    * 1.6 * $brandBonus;

                $scored[$otherId] = [
                    'confidence' => $attrConfidence,
                    'lift' => $attrLift,
                    'leverage' => $attrLeverage,
                    'conviction' => $attrConviction,
                    'price_sim' => $priceSim,
                    'category' => strtolower($other['category'] ?? 'shoes'),
                    'score' => $score,
                ];
            }
        }

        uasort($scored, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $topIds = $this->pickTopIds($scored, $currentCategory, $limit);
        $recommendations = [];
        foreach ($topIds as $oid) {
            if (!isset($allMap[$oid])) {
                continue;
            }
            $recommendations[] = [
                'shoe' => $allMap[$oid],
                'confidence' => $scored[$oid]['confidence'],
                'lift' => $scored[$oid]['lift'],
                'leverage' => $scored[$oid]['leverage'],
                'conviction' => $scored[$oid]['conviction'],
                'price_sim' => $scored[$oid]['price_sim'],
                'score' => $scored[$oid]['score'],
            ];
        }

        return [
            'title' => $title,
            'recommendations' => $recommendations,
        ];
    }

    private function loadBaskets()
    {
        $baskets = [];
        $result = $this->db->query(
            "SELECT user_id, shoe_id FROM purchase_request WHERE user_id IS NOT NULL AND user_id != ''"
        );
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $baskets[$row['user_id']][$row['shoe_id']] = true;
            }
        }
        return $baskets;
    }

    private function isValidTarget($candidateCategory, array $targetCategories)
    {
        $cat = strtolower(trim($candidateCategory ?? 'shoes'));
        if ($cat === 'accessories') {
            $cat = 'socks';
        }
        return in_array($cat, $targetCategories, true);
    }

    private function pickTopIds(array $scored, $currentCategory, $limit)
    {
        $topIds = [];
        if ($currentCategory === 'shoes') {
            $socksPicked = 0;
            $cleanerPicked = 0;
            foreach (array_keys($scored) as $oid) {
                $cat = $scored[$oid]['category'] ?? '';
                if ($cat === 'accessories') {
                    $cat = 'socks';
                }
                if ($cat === 'socks' && $socksPicked < 2) {
                    $topIds[] = $oid;
                    $socksPicked++;
                } elseif ($cat === 'shoe_care' && $cleanerPicked < 2) {
                    $topIds[] = $oid;
                    $cleanerPicked++;
                }
                if (count($topIds) >= $limit) {
                    break;
                }
            }
            if (count($topIds) < $limit) {
                foreach (array_keys($scored) as $oid) {
                    if (in_array($oid, $topIds, true)) {
                        continue;
                    }
                    $topIds[] = $oid;
                    if (count($topIds) >= $limit) {
                        break;
                    }
                }
            }
        } else {
            $topIds = array_slice(array_keys($scored), 0, $limit);
        }
        return $topIds;
    }
}
