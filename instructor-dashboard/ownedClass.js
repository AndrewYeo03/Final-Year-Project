document.addEventListener("DOMContentLoaded", function () {
    //Binding the "Manage Students" button event
    document.querySelectorAll(".manage-students-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const className = this.dataset.class;

            document.getElementById("className").value = className;
            document.getElementById("currentClassName").textContent = className;

            // 调试：检查按钮点击时传递的 className
            console.log("Button clicked:", className);

            //AJAX request: Retrieve student list
            loadStudents(className);

            //Show modal box
            const modal = new bootstrap.Modal(document.getElementById("manageStudentsModal"));
            modal.show();
        });
    });

    //Bind add student event
    document.getElementById("addStudentForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const studentId = document.getElementById("studentId").value.trim();
        const className = document.getElementById("className").value;

        if (!studentId) {
            alert("Student ID cannot be empty.");
            return;
        }

        // Show confirmation prompt
        const confirmAdd = confirm(`Are you sure you want to add Student ID: ${studentId} to Class: ${className}?`);
        if (!confirmAdd) {
            return; // User canceled the operation
        }

        // Send AJAX request to add the student
        const formData = new FormData(this);

        fetch("add_students.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json()) // Expect JSON response
            .then((data) => {
                if (data.error) throw new Error(data.error);

                alert(data.message); // Show success message

                // Reload student list
                loadStudents(className);

                // Clear the form
                document.getElementById("addStudentForm").reset();
            })
            .catch((error) => alert(error.message));
    });

    //Dynamic binding delete student button event
    function bindDeleteStudentButtons() {
        document.querySelectorAll(".delete-student-btn").forEach((delBtn) => {
            delBtn.addEventListener("click", function () {
                const studentId = this.dataset.studentId;
                const className = this.dataset.class;
                const studentName = this.closest("tr").children[1].textContent; //Get student's name

                //Display confirmation box
                const confirmDelete = confirm(`Are you sure you want to delete Student ID: ${studentId}, Name: ${studentName}?`);
                if (!confirmDelete) {
                    return; //User cancel action
                }

                //Once confirm proceed delete
                fetch("delete_students.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `className=${encodeURIComponent(className)}&studentId=${encodeURIComponent(studentId)}`,
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then((data) => {
                        if (data.error) throw new Error(data.error);

                        alert(`Student ID: ${studentId}, Name: ${studentName} has been successfully deleted.`);
                        loadStudents(className); //Reload student table
                    })
                    .catch((error) => alert(error.message));
            });
        });
    }

    //Loading student list
    function loadStudents(className) {
        fetch("get_students.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `className=${encodeURIComponent(className)}`,
        })
            .then((response) => response.json())
            .then((data) => {

                // 调试：检查加载的学生数据
                console.log("Students loaded:", data.students);


                const tableBody = document.getElementById("studentsTableBody");
                tableBody.innerHTML = ""; //Clear table

                if (data.students && data.students.length > 0) {
                    data.students.forEach((student) => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                        <td>${student.student_id}</td>
                        <td>${student.student_name}</td>
                        <td>
                            <button class="btn btn-danger delete-student-btn" 
                                    data-student-id="${student.student_id}" 
                                    data-class="${className}">
                                Delete
                            </button>
                        </td>
                    `;
                        tableBody.appendChild(row);
                    });

                    //Dynamically bind delete button event
                    bindDeleteStudentButtons();
                } else {
                    //If there are no students, show empty state
                    const emptyRow = document.createElement("tr");
                    emptyRow.innerHTML = `<td colspan="3" class="text-center">No students in this class.</td>`;
                    tableBody.appendChild(emptyRow);
                }
            });
    }
});
