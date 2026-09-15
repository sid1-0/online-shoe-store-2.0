# Myshoes Class Diagram

Below is the UML Class Diagram for the PHP classes found in `Myshoes/classes/`. You can view this diagram using any Markdown viewer that supports Mermaid syntax (like GitHub or modern IDE plugins).

```mermaid
classDiagram

    class Database {
        - static $instance : Database
        - $conn : mysqli
        - __construct()
        + static getInstance() : Database
        + getConnection() : mysqli
        - __clone()
    }

    class User {
        - $db : mysqli
        + __construct()
        + static startSession()
        + static isLoggedIn() : bool
        + static requireLogin(redirect: string)
        + static getId() : int
        + static getUsername() : string
        + static logout(redirect: string)
        + findByUsername(username: string) : array
        + usernameExists(username: string) : bool
        + emailExists(email: string) : bool
        + login(username: string, password: string) : bool|string
        + register(username: string, email: string, password: string) : bool|string
    }

    class Product {
        - $db : mysqli
        + __construct()
        + findById(id: int) : array
        + getAll() : array
        + getAllMappedById() : array
        + getByCategory(category: string) : array
        + getByBrand(brand: string) : array
        + getByBrandAndCategory(brand: string, category: string) : array
        + create(name, brand, price, description, image, status, category, createdBy) : int|bool
        + update(id, name, brand, price, description, status, image, category, updatedBy) : bool
        + delete(id: int) : bool
        + getLastError() : string
    }

    class Cart {
        + __construct()
        + add(shoeId: int, qty: int)
        + remove(shoeId: int) : bool
        + clear()
        + isEmpty() : bool
        + getItems() : array
        + countItems() : int
        + getDetailedItems() : array
        + getTotal() : float
    }

    class AssociationMiner {
        - $db : mysqli
        - $productModel : Product
        + __construct()
        + getRecommendations(shoeId: int, limit: int) : array
        - loadBaskets() : array
        - isValidTarget(candidateCategory: string, targetCategories: array) : bool
        - pickTopIds(scored: array, currentCategory: string, limit: int) : array
    }

    %% Relationships
    User --> Database : uses
    Product --> Database : uses
    AssociationMiner --> Database : uses
    AssociationMiner --> Product : has-a
    Cart ..> Product : uses (dependency)

```
