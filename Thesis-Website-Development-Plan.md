### **Phase 1: Core Functionality Implementation**  
**Duration:** Weeks 1–2 (Mid-March)  
**Goal:** Build essential features for user registration, payment, NFC assignment, and bike unlocking.  

#### **Tasks:**  
<!-- 1. **User Authentication (Week 1)**  
   - Create registration/login pages with form validation.  
   - Implement password hashing (e.g., `password_hash()` in PHP).  
   - Add session management for logged-in users.  

2. **Bike Rental Flow (Week 1–2)**  
   - Design a user interface for browsing available bikes (pull from `bikes` table).  
   - Add a "Rent Now" button to reserve a bike.  
   - **Payment Integration:**  
     - Integrate a payment gateway (e.g., PayPal Sandbox or Stripe Test Mode).  
     - Create a "Cash Payment" option:  
       - Allow users to select cash payment, which sets the transaction status to `pending_approval`.  
       - Add an admin interface to approve cash payments and mark transactions as `completed`.   -->

3. **NFC Tag Assignment (Week 2)**  
   - Develop logic to assign an available NFC tag (from `nfc_tags` table) after successful payment.  
   - Update the `transactions` table to link the NFC tag ID to the user.  
   - Display the assigned NFC tag details to the user (e.g., a mock NFC ID or QR code).  

4. **Bike Unlocking Simulation (Week 2)**  
   - Create a page where users input their NFC tag ID to "unlock" a bike.  
   - Update the `bikes` table to mark the bike as `in_use`.  

---

### **Phase 2: Admin and User Features**  
**Duration:** Weeks 3–4 (Late March)  
**Goal:** Expand functionality for admins, users, and bike returns.  

#### **Tasks:**  
5. **Admin Dashboard (Week 3)**  
   - Build a dashboard for admins to:  
     - Approve cash payments.  
     - Manage bikes (add/remove/update).  
     - Manage NFC tags (assign/unassign).  
     - View all transactions and users.  

6. **Bike Return System (Week 3)**  
   - Add a "Return Bike" button in the user profile.  
   - Calculate rental duration and fees (if applicable).  
   - Update the `bikes` and `nfc_tags` tables to mark items as `available`.  

7. **User Profile (Week 4)**  
   - Create a profile page where users can:  
     - View current/past rentals.  
     - Update personal details (email, password).  
     - See payment history (from `transactions` table).  

8. **Notifications (Week 4)**  
   - Send email/SMS alerts for:  
     - Successful payment.  
     - NFC tag assignment.  
     - Bike return confirmation.  
   - Use PHP libraries like **PHPMailer** for emails.  

---

### **Phase 3: Security & Testing**  
**Duration:** Weeks 5–6 (Early April)  
**Goal:** Ensure robustness, security, and usability.  

#### **Tasks:**  
9. **Security Enhancements (Week 5)**  
   - Sanitize all user inputs to prevent SQL injection/XSS attacks.  
   - Use prepared statements (e.g., `mysqli_prepare()`).  
   - Encrypt sensitive data (e.g., user emails, NFC tag IDs).  
   - Implement HTTPS (if not already done).  

10. **Testing (Week 5–6)**  
    - **Unit Testing:**  
      - Test user registration, payment, NFC assignment, and bike unlocking.  
    - **Edge Cases:**  
      - Test scenarios like double payments, expired NFC tags, and bike unavailability.  
    - **User Testing:**  
      - Have peers test the system and provide feedback.  

11. **Bug Fixes & Polish (Week 6)**  
    - Fix any issues found during testing.  
    - Optimize database queries for performance.  
    - Improve UI/UX (e.g., loading times, error messages).  

---

### **Phase 4: Final Preparation (April Testing)**  
**Duration:** 1 Week Before Testing  
**Goal:** Finalize documentation and demo.  

#### **Tasks:**  
12. **Documentation**  
    - Write a user manual (how to rent, pay, return bikes).  
    - Prepare a technical report for your thesis (database schema, system architecture).  

13. **Demo Preparation**  
    - Record a video demo of the full workflow (payment → NFC assignment → unlocking → return).  
    - Prepare test cases to showcase during the presentation.  

---

### **Timeline Summary**  
| **Week** | **Focus**                          |  
|----------|------------------------------------|  
| 1–2      | Core Features (Auth, Payment, NFC) |  
| 3–4      | Admin & User Features              |  
| 5–6      | Security, Testing, Bug Fixes       |  
| Final Week| Documentation & Demo Prep          |  

---

### **Tools & Resources**  
- **Payment Gateways:** Use **Stripe** or **PayPal Sandbox** for testing.  
- **NFC Simulation:** Mock NFC tags using unique IDs (e.g., generate random 10-digit codes).  
- **PHP Libraries:**  
  - PHPMailer (for emails).  
  - Stripe PHP SDK (for payments).  

---

### **Risks & Mitigation**  
- **Payment Gateway Delays**: Start with a sandbox/test environment early.  
- **Hardware Limitations**: Simulate NFC unlocking since physical hardware integration might be out of scope.  
- **Security Flaws**: Perform multiple rounds of security testing.  

Let me know if you need help with specific code snippets (e.g., payment integration, session management) or further refinements! 🚴♂️💻
