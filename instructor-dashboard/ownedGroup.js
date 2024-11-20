document.addEventListener("DOMContentLoaded", function () {
    //Binding the "Manage Students" button event
    document.querySelectorAll(".manage-students-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const className = this.dataset.class;
            document.getElementById("className").value = className;

            //AJAX request: Retrieve the student list
            fetch("get_students.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `className=${encodeURIComponent(className)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    const tableBody = document.getElementById("studentsTableBody");
                    tableBody.innerHTML = ""; //Clear table

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

                    //Rebind dynamically generated delete button events
                    bindDeleteStudentButtons();
                });

            //Show modal box
            new bootstrap.Modal(document.getElementById("manageStudentsModal")).show();
        });
    });

    //Bind add student event
    document.getElementById("addStudentForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("add_students.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json()) // Expect JSON response
            .then((data) => {
                if (data.error) throw new Error(data.error);
                loadStudents(className); // Reload Student List
            })
            .catch((error) => alert(error.message));
    });

    //Dynamic binding delete student button event
    function bindDeleteStudentButtons() {
        document.querySelectorAll(".delete-student-btn").forEach((delBtn) => {
            delBtn.addEventListener("click", function () {
                const studentId = this.dataset.studentId;
                const className = this.dataset.class;
                const studentName = this.closest("tr").children[1].textContent; // 获取学生名字
    
                // 显示确认提示框
                const confirmDelete = confirm(`Are you sure you want to delete Student ID: ${studentId}, Name: ${studentName}?`);
                if (!confirmDelete) {
                    return; // 用户取消操作
                }
    
                // 用户确认后发送请求
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
                        loadStudents(className); // 重新加载学生列表
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
                const tableBody = document.getElementById("studentsTableBody");
                tableBody.innerHTML = ""; //Clear table

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

                //Rebind dynamically generated delete button events
                bindDeleteStudentButtons();
            });
    }
});