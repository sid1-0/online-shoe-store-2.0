# Myshoes Order Lifecycle State Diagram

Below is the UML State Diagram mapping the lifecycle of an Order in your e-commerce system, specifically focusing on the eSewa payment integration flow. You can view this diagram using a Markdown viewer that supports Mermaid syntax.

```mermaid
stateDiagram-v2
    %% Initial state
    [*] --> ShoppingCart : User adds items

    %% Checkout process
    ShoppingCart --> OrderCreated : User clicks checkout

    %% Payment flow
    OrderCreated --> PendingPayment : Initiate eSewa payment
    
    %% eSewa Callbacks
    PendingPayment --> PaymentSuccessful : eSewa callback (esewa_success.php)
    PendingPayment --> PaymentFailed : eSewa callback (esewa_failed.php)
    
    %% Handling failed payment
    PaymentFailed --> ShoppingCart : User retries payment
    
    %% Fulfillment process
    PaymentSuccessful --> Processing : Admin verifies payment
    Processing --> Shipped : Admin marks as shipped
    Shipped --> Delivered : Customer receives shoes
    
    %% Cancellation paths
    OrderCreated --> Cancelled : User/Admin cancels
    PendingPayment --> Cancelled : Payment timeout / User cancels
    
    %% Final states
    Delivered --> [*]
    Cancelled --> [*]
```
