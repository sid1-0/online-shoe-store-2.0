# Myshoes Deployment Diagram

Below is the UML Deployment Diagram illustrating the physical hardware and software environment where the Myshoes system is deployed (e.g., typically a LAMP/XAMPP stack). You can view this diagram using a Markdown viewer that supports Mermaid syntax.

```mermaid
flowchart TD
    %% Define Nodes (Hardware/Execution Environments)
    
    subgraph Client_Device ["Client Device (PC, Mobile, Tablet)"]
        Browser["Web Browser\n(Chrome, Firefox, Safari)"]
    end

    subgraph Web_Server_Node ["Web Server Node (Apache)"]
        App_Container["PHP Application Container\n(PHP 8.x)"]
        subgraph Web_Artifacts ["Myshoes Application"]
            PHP_Files[("PHP Files\n(.php)")]
            Assets[("Static Assets\n(CSS, JS, Images)")]
        end
        App_Container --> PHP_Files
        App_Container --> Assets
    end

    subgraph Database_Server_Node ["Database Server Node (MySQL)"]
        DB_Engine["MySQL Engine"]
        subgraph DB_Artifacts ["Database"]
            Schema[("shoes Schema")]
        end
        DB_Engine --> Schema
    end

    subgraph External_Node ["External API Server"]
        eSewa_API["eSewa Payment API"]
    end

    %% Define Communication Paths
    Browser -- "HTTP/HTTPS (TCP/IP)" --> App_Container
    Browser -- "HTTPS (Redirect/Callback)" --> eSewa_API
    App_Container -- "MySQL Protocol (TCP 3306)" --> DB_Engine
    App_Container -- "HTTPS (Server-to-Server API calls)" --> eSewa_API

    %% Styling
    classDef node fill:#e0f7fa,stroke:#006064,stroke-width:2px;
    classDef external fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    
    class Client_Device,Web_Server_Node,Database_Server_Node node;
    class External_Node external;
```
