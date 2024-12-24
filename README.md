Cyber Range Platform for Cybersecurity Training 🚀
This repository hosts the Cyber Range Platform, a training environment designed for TAR UMT students to strengthen their cybersecurity skills. Through realistic scenarios, students can practice various attack and defense techniques to build practical knowledge in a controlled environment.

📋 Features
Scenario-Based Training:
Engage in diverse exercises, including SSH vulnerabilities, DHCP spoofing, XSS attacks, and more.
Interactive Exercises:
Practice hands-on tasks like network scanning, brute force attacks, and flag submission.
Progress Tracking:
Monitor your learning journey and access detailed progress reports.
Fail-Safe Design:
Includes brute-force protection and real-time feedback systems.
Seamless Integration:
Fully integrated with the university's Learning Management System (LMS).
Instructor Dashboard:
Manage student activities, evaluate submitted solutions, and review overall performance.
🧑‍💻 Test Accounts
Admin Account
Username: ADMIN_USER
Email: admin@example.com
Password: password
Instructor Accounts
Name: OOI CHUN PEW
Email: cpooi@tarc.edu.my
Password: password
Name: JESSIE TEOH POH LIN
Email: plteoh@tarc.edu.my
Password: password
Student Accounts
Name: YEO JUN KEN
Email: yeojk-wm21@student.tarc.edu.my
Password: password
Name: TAN YI YANG
Email: tanyy-wm21@student.tarc.edu.my
Password: password
Name: IAN LAI WEN KYE
Email: ianlwk-wm21@student.tarc.edu.my
Password: password
📂 Project UI Previews
🏠 Dashboard for Scenarios
Navigate and select exercises directly from the user-friendly dashboard.


🛠️ Scenario Execution
Follow step-by-step guidance for each scenario.


✅ Flag Submission
Easily submit obtained flags and logs to complete exercises.


🔧 Installation and Setup
Clone Repository:

bash
Copy code
git clone https://github.com/your-repo/cyber-range-platform.git
cd cyber-range-platform
Set Up Environment:

Ensure Docker and Docker Compose are installed.
Run the following command to start the platform:
bash
Copy code
docker-compose up --build
Access Application:

Navigate to http://localhost:8000 in your browser.
Use the provided test accounts to log in.
Database Configuration:
Run migrations and seed initial data:

bash
Copy code
python manage.py migrate  
python manage.py loaddata test_accounts.json
📄 User Manual
Logging In
Open the application in your browser.
Enter your assigned credentials to access the platform.
Starting a Scenario
Go to the "Scenario List" from the dashboard.
Select a scenario and click Start.
Follow the instructions provided to complete the scenario.
Submitting Results
Upon completion, go to the "Submit Flag" page.
Enter the obtained flag or credentials and upload required logs.
Click Submit to finalize the exercise.
Tracking Progress
Students can view their achievements in the "My Progress" section.
Instructors can monitor student activities through the dashboard.
