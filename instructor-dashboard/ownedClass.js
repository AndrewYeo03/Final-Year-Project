document.addEventListener("DOMContentLoaded", function () {
    // Function to bind "Manage Students" button events
    function bindManageStudentsButtons() {
        document.querySelectorAll(".manage-students-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                const className = this.dataset.class;

                // Update modal content
                document.getElementById("className").value = className;
                document.getElementById("currentClassName").textContent = className;

                // 调试：检查按钮点击时传递的 className
                console.log("Button clicked:", className);

                // AJAX request to load student list
                loadStudents(className);

                // Show modal box
                const modal = new bootstrap.Modal(document.getElementById("manageStudentsModal"));
                modal.show();
            });
        });
    }

    // Function to load students dynamically via AJAX
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
                tableBody.innerHTML = ""; // Clear table

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

                    // Dynamically bind delete button events
                    bindDeleteStudentButtons();
                } else {
                    // If there are no students, show empty state
                    const emptyRow = document.createElement("tr");
                    emptyRow.innerHTML = `<td colspan="3" class="text-center">No students in this class.</td>`;
                    tableBody.appendChild(emptyRow);
                }
            });
    }

    // Function to bind delete student button events
    function bindDeleteStudentButtons() {
        document.querySelectorAll(".delete-student-btn").forEach((delBtn) => {
            delBtn.addEventListener("click", function () {
                const studentId = this.dataset.studentId;
                const className = this.dataset.class;
                const studentName = this.closest("tr").children[1].textContent; // Get student's name

                // Show confirmation box
                const confirmDelete = confirm(`Are you sure you want to delete Student ID: ${studentId}, Name: ${studentName}?`);
                if (!confirmDelete) return; // User canceled action

                // Proceed with delete request
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
                        loadStudents(className); // Reload student table
                    })
                    .catch((error) => alert(error.message));
            });
        });
    }

    // Binding add student event
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
        if (!confirmAdd) return;

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

    // Initial binding of "Manage Students" buttons
    bindManageStudentsButtons();
});
