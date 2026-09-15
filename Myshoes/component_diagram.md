# Myshoes Component Diagram

Below is the UML Component Diagram mapping the high-level architecture and the dependencies between various modules of the Myshoes system, including the external eSewa payment integration. You can view this diagram using a Markdown viewer that supports Mermaid syntax.

```mermaid
%%{init: {'theme': 'default', 'themeVariables': { 'componentBkg': '#f4f4f4'}}}%%
flowchart TB
    %% External Actors/Systems
    User((Web User))
    eSewa[/"eSewa Payment Gateway (External API)"/]
    MySQL[("MySQL Database\n(shoes db)")]

    %% Main System Boundary
    subgraph Myshoes_System ["Myshoes Web Application"]
        
        %% UI Layer
        UI["Web Interface\n(PHP Pages, HTML/CSS)"]
        
        %% Business Logic Components
        AuthComp["Authentication Component\n(User.php)"]
        CatalogComp["Product Catalog Component\n(Product.php)"]
        CartComp["Shopping Cart & Checkout\n(Cart.php, place_order.php)"]
        RecComp["Recommendation Engine\n(AssociationMiner.php)"]
        DBLayer["Data Access Component\n(Database.php)"]
        
        %% Internal Dependencies
        UI -.-> AuthComp
        UI -.-> CatalogComp
        UI -.-> CartComp
        UI -.-> RecComp
        
        AuthComp --> DBLayer
        CatalogComp --> DBLayer
        CartComp -.-> CatalogComp
        RecComp --> CatalogComp
        RecComp --> DBLayer
    end

    %% External Interactions
    User === UI
    CartComp -- "Payment Request / Callback" --> eSewa
    DBLayer === MySQL
```
