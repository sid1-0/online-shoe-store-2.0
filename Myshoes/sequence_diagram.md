# Myshoes eSewa Checkout Sequence Diagram

Below is the UML Sequence Diagram illustrating the interactions between the User, the Myshoes System, the eSewa Payment Gateway, and the Database during the checkout process. You can view this diagram using a Markdown viewer that supports Mermaid syntax.

```mermaid
sequenceDiagram
    autonumber
    actor User
    participant System as Myshoes System
    participant Database
    participant eSewa as eSewa Gateway

    User->>System: View Cart & Click "Checkout"
    System->>Database: Save Order Details (Status: Pending)
    Database-->>System: Return Order ID
    
    System->>User: Redirect to eSewa Payment Page
    User->>eSewa: Enter Payment Credentials & Confirm
    
    alt Payment Successful
        eSewa-->>System: Redirect to esewa_success.php (with refId)
        System->>eSewa: API Call to verify payment (refId, amount)
        eSewa-->>System: Return Verification Status (Success)
        System->>Database: Update Order Status (Status: Processing)
        Database-->>System: Acknowledge Update
        System->>User: Display "Payment Successful, Order Placed!"
    else Payment Failed/Cancelled
        eSewa-->>System: Redirect to esewa_failed.php
        System->>Database: Update Order Status (Status: Cancelled/Failed)
        Database-->>System: Acknowledge Update
        System->>User: Display "Payment Failed, Please Try Again"
    end
```
