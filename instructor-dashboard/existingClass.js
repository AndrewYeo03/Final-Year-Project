document.addEventListener("DOMContentLoaded", function () {
    //Use event delegation to bind click events
    document.getElementById("datatablesSimple").addEventListener("click", function (e) {
        //Check if the "Manage Students" button was clicked
        if (e.target.classList.contains("manage-students-btn")) {
            const btn = e.target; //The button currently clicked
            const className = btn.dataset.class;

            //Update modal content
            document.getElementById("className").value = className;
            document.getElementById("currentClassName").textContent = className;

            //AJAX request to load the student list
            loadStudents(className);

            //Show modal box
            const modal = new bootstrap.Modal(document.getElementById("manageStudentsModal"));
            modal.show();
        }

        //Check if the "Archive Class" button was clicked
        if (e.target.classList.contains("archive-class-btn")) {
            const className = e.target.dataset.class;

            //Confirm the save operation
            const confirmArchive = confirm(`Are you sure you want to archive the class "${className}"?`);
            if (!confirmArchive) return;

            //Send an archive request to the backend
            fetch("archive_class.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `className=${encodeURIComponent(className)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) throw new Error(data.error);
                    alert(`Class "${className}" has been successfully archived.`);
                    location.reload(); //Reload the page
                })
                .catch((error) => alert(error.message));
        }
    });

    //Bind the submit event of the add student form
    document.getElementById("addStudentForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const studentId = document.getElementById("studentId").value.trim();
        const className = document.getElementById("className").value;

        if (!studentId) {
            alert("Student ID cannot be empty.");
            return;
        }

        //Confirm the add operation
        const confirmAdd = confirm(`Are you sure you want to add Student ID: ${studentId} to Class: ${className}?`);
        if (!confirmAdd) return;

        //Send an AJAX request to add a student
        const formData = new FormData(this);

        fetch("add_students.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json()) //Expecting JSON response
            .then((data) => {
                if (data.error) throw new Error(data.error);

                alert(data.message); //Display success information

                //Reload Student List
                loadStudents(className);

                //Clear table
                document.getElementById("addStudentForm").reset();
            })
            .catch((error) => alert(error.message));
    });

    //Dynamically load student functions
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

                    //Bind event for delete button
                    bindDeleteStudentButtons();
                } else {
                    //If there are no students, show empty state
                    const emptyRow = document.createElement("tr");
                    emptyRow.innerHTML = `<td colspan="3" class="text-center">No students in this class.</td>`;
                    tableBody.appendChild(emptyRow);
                }
            });
    }

    //Bind the click event of the delete student button
    function bindDeleteStudentButtons() {
        document.querySelectorAll(".delete-student-btn").forEach((delBtn) => {
            delBtn.addEventListener("click", function () {
                const studentId = this.dataset.studentId;
                const className = this.dataset.class;
                const studentName = this.closest("tr").children[1].textContent; // 获取学生名称

                //Show Confirmation Box
                const confirmDelete = confirm(`Are you sure you want to delete Student ID: ${studentId}, Name: ${studentName}?`);
                if (!confirmDelete) return;

                //Send a removal request
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
});
