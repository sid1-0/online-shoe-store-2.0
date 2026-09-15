git commit -m "Initial commit"# Myshoes Purchasing Activity Diagram

Below is the UML Activity Diagram illustrating the step-by-step user flow for browsing, adding items to the cart, and completing a purchase via eSewa. You can view this diagram using a Markdown viewer that supports Mermaid syntax.

```mermaid
flowchart TD
    %% Start Node
    Start((Start)) --> Browse[Browse Products]
    
    %% Browsing & Selection
    Browse --> Select[Select Product Details]
    Select --> AddToCart{Add to Cart?}
    AddToCart -- No --> Browse
    AddToCart -- Yes --> Cart[View Cart]
    
    %% Checkout Decision
    Cart --> Checkout{Proceed to Checkout?}
    Checkout -- No --> Browse
    Checkout -- Yes --> LoginCheck{Is Logged In?}
    
    %% Authentication
    LoginCheck -- No --> Login[Login / Register]
    Login --> Checkout
    
    %% Payment Process
    LoginCheck -- Yes --> PlaceOrder[Place Order]
    PlaceOrder --> PayEsewa[Redirect to eSewa Payment]
    
    PayEsewa --> PaymentStatus{Payment Status}
    
    %% Payment Outcomes
    PaymentStatus -- Failed/Cancelled --> CancelOrder[Mark Order as Failed]
    CancelOrder --> Retry{Retry Payment?}
    Retry -- Yes --> PayEsewa
    Retry -- No --> Cart
    
    PaymentStatus -- Success --> VerifyPayment[Verify Payment (esewa_success.php)]
    VerifyPayment --> VerificationCheck{Is Valid?}
    
    VerificationCheck -- No --> SuspectFraud[Log Suspicious Activity]
    SuspectFraud --> ContactSupport[Contact Customer Support]
    ContactSupport --> End((End))
    
    VerificationCheck -- Yes --> CompleteOrder[Update Order to Processing]
    CompleteOrder --> SendEmail[Display Success & Send Confirmation]
    SendEmail --> End
```
