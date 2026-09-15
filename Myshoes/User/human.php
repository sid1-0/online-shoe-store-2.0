<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Carousel</title>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: relative;
            z-index: 100;
        }

        .carousel {
            position: relative;
            max-width: 1400px;
            margin: 40px auto;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        /* List item */
        .list {
            position: relative;
            height: 600px;
        }

        .item {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease, transform 0.8s ease;
            transform: scale(0.9);
            display: flex;
            overflow: hidden;
        }

        .item.active {
            opacity: 1;
            transform: scale(1);
            z-index: 10;
        }

        .item img {
            width: 50%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .content {
            width: 50%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .author {
            font-size: 1rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #212529;
            margin-bottom: 15px;
            line-height: 1.2;
            position: relative;
            padding-bottom: 15px;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2980b9);
        }

        .topic {
            font-size: 1.2rem;
            font-weight: 600;
            color: #3498db;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .des {
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .buttons {
            display: flex;
            gap: 15px;
        }

        .buttons button {
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .buttons button:first-child {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .buttons button:first-child:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(41, 128, 185, 0.3);
        }

        .buttons button:last-child {
            background: transparent;
            color: #3498db;
            border: 2px solid #3498db;
        }

        .buttons button:last-child:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        /* Thumbnail */
        .thumbnail {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            background: #fff;
            border-top: 1px solid #eee;
        }

        .thumbnail .item {
            position: relative;
            width: 150px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.6;
            transform: scale(1);
            transition: all 0.3s ease;
        }

        .thumbnail .item.active {
            opacity: 1;
            border: 3px solid #3498db;
        }

        .thumbnail .item:hover {
            opacity: 0.8;
        }

        .thumbnail .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail .content {
            display: none;
        }

        /* Arrows */
        .arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 100;
        }

        .arrows button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.7);
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .arrows button:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: scale(1.1);
        }

        /* Time running */
        .time {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 5px;
            background: linear-gradient(90deg, #3498db, #2980b9);
            z-index: 100;
            animation: timeRunning 5s linear infinite;
        }

        @keyframes timeRunning {
            0% { width: 0; }
            100% { width: 100%; }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .list {
                height: 500px;
            }
            
            .content {
                padding: 40px;
            }
            
            .title {
                font-size: 2.2rem;
            }
            
            .des {
                font-size: 1rem;
            }
        }

        @media (max-width: 768px) {
            .item {
                flex-direction: column;
            }
            
            .item img, .content {
                width: 100%;
                height: 50%;
            }
            
            .list {
                height: 700px;
            }
            
            .content {
                padding: 30px;
            }
            
            .title {
                font-size: 2rem;
                padding-bottom: 10px;
            }
            
            .des {
                margin-bottom: 20px;
                max-height: 100px;
                overflow-y: auto;
            }
            
            .thumbnail {
                overflow-x: auto;
                justify-content: flex-start;
                padding: 15px;
            }
            
            .thumbnail .item {
                min-width: 120px;
            }
        }

        @media (max-width: 576px) {
            .list {
                height: 600px;
            }
            
            .content {
                padding: 20px;
            }
            
            .title {
                font-size: 1.8rem;
            }
            
            .author, .topic {
                font-size: 0.9rem;
            }
            
            .buttons {
                flex-direction: column;
            }
            
            .buttons button {
                width: 100%;
            }
            
            .arrows button {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <header>
    </header>

    <!-- carousel -->
    <div class="carousel">
        <!-- list item -->
        <div class="list">
            <div class="item active">
                <img src="images/img1.jpg" alt="Carousel Image 1">
                <div class="content">
                    <div class="author">LUNDEV</div>
                    <div class="title">DESIGN SLIDER</div>
                    <div class="topic">ANIMAL</div>
                    <div class="des">
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                    </div>
                    <div class="buttons">
                        <button>SEE MORE</button>
                        <button>SUBSCRIBE</button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/shoes.jpg" alt="Carousel Image 2">
                <div class="content">
                    <div class="author">LUNDEV</div>
                    <div class="title">DESIGN SLIDER</div>
                    <div class="topic">ANIMAL</div>
                    <div class="des">
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                    </div>
                    <div class="buttons">
                        <button>SEE MORE</button>
                        <button>SUBSCRIBE</button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/img3.jpg" alt="Carousel Image 3">
                <div class="content">
                    <div class="author">LUNDEV</div>
                    <div class="title">DESIGN SLIDER</div>
                    <div class="topic">ANIMAL</div>
                    <div class="des">
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                    </div>
                    <div class="buttons">
                        <button>SEE MORE</button>
                        <button>SUBSCRIBE</button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/hero.jpg" alt="Carousel Image 4">
                <div class="content">
                    <div class="author">LUNDEV</div>
                    <div class="title">DESIGN SLIDER</div>
                    <div class="topic">ANIMAL</div>
                    <div class="des">
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                    </div>
                    <div class="buttons">
                        <button>SEE MORE</button>
                        <button>SUBSCRIBE</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- list thumbnail -->
        <div class="thumbnail">
            <div class="item active">
                <img src="images/img1.jpg" alt="Thumbnail 1">
                <div class="content">
                    <div class="title">
                        Name Slider
                    </div>
                    <div class="description">
                        Description
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/shoes.jpg" alt="Thumbnail 2">
                <div class="content">
                    <div class="title">
                        Name Slider
                    </div>
                    <div class="description">
                        Description
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/img3.jpg" alt="Thumbnail 3">
                <div class="content">
                    <div class="title">
                        Name Slider
                    </div>
                    <div class="description">
                        Description
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/hero.jpg" alt="Thumbnail 4">
                <div class="content">
                    <div class="title">
                        Name Slider
                    </div>
                    <div class="description">
                        Description
                    </div>
                </div>
            </div>
        </div>
        <!-- next prev -->

        <div class="arrows">
            <button id="prev"><</button>
            <button id="next">></button>
        </div>
        <!-- time running -->
        <div class="time"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all carousel items
            const items = document.querySelectorAll('.carousel .list .item');
            const thumbnails = document.querySelectorAll('.carousel .thumbnail .item');
            const prevBtn = document.getElementById('prev');
            const nextBtn = document.getElementById('next');
            
            let activeIndex = 0;
            const timeRunning = 5000;
            let timeAutoNext = timeRunning;
            
            // Initialize first item as active
            items[activeIndex].classList.add('active');
            thumbnails[activeIndex].classList.add('active');
            
            // Function to show specific item
            function showItem(index) {
                // Remove active class from all items
                items.forEach(item => item.classList.remove('active'));
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                
                // Add active class to current item
                items[index].classList.add('active');
                thumbnails[index].classList.add('active');
                
                // Reset timer
                timeAutoNext = timeRunning;
            }
            
            // Next button click
            nextBtn.addEventListener('click', function() {
                activeIndex = (activeIndex + 1) % items.length;
                showItem(activeIndex);
            });
            
            // Previous button click
            prevBtn.addEventListener('click', function() {
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                showItem(activeIndex);
            });
            
            // Thumbnail click
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', function() {
                    activeIndex = index;
                    showItem(activeIndex);
                });
            });
            
            // Auto slide
            let autoSlide = setInterval(() => {
                nextBtn.click();
            }, timeRunning);
            
            // Reset interval on user interaction
            const carousel = document.querySelector('.carousel');
            carousel.addEventListener('mouseenter', function() {
                clearInterval(autoSlide);
            });
            
            carousel.addEventListener('mouseleave', function() {
                autoSlide = setInterval(() => {
                    nextBtn.click();
                }, timeRunning);
            });
        });
    </script>
</body>
</html>
