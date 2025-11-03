//form validation

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addStudentForm');
    const studentIdInput = document.getElementById('studentId');
    const lastNameInput = document.getElementById('lastName');
    const firstNameInput = document.getElementById('firstName');
    const emailInput = document.getElementById('email');
    
    //check student id
    function validateStudentId(studentId) {
        if (studentId.trim() === '') {
            return 'Student ID is required.';
        }
        if (!/^\d+$/.test(studentId)) {
            return 'Student ID must contain only numbers.';
        }
        return '';
    }

    //validate name fields
    function validateName(name, fieldName) {
        if (name.trim() === '') {
            return fieldName + ' is required.';
        }
        if (!/^[a-zA-Z\s]+$/.test(name)) {
            return fieldName + ' must contain only letters.';
        }
        return '';
    }
    
    //email validation
    function validateEmail(email) {
        if (email.trim() === '') {
            return 'Email is required.';
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return 'Email must be in a valid format (e.g., name@example.com).';
        }
        return '';
    }
    
    // show error
    function showError(input, errorElement, message) {
        input.classList.add('error');
        errorElement.textContent = message;
    }
    
    // clear error
    function clearError(input, errorElement) {
        input.classList.remove('error');
        errorElement.textContent = '';
    }
    
    // validate on blur
    studentIdInput.addEventListener('blur', function() {
        const error = validateStudentId(this.value);
        const errorElement = document.getElementById('studentIdError');
        if (error) {
            showError(this, errorElement, error);
        } else {
            clearError(this, errorElement);
        }
    });
    
    lastNameInput.addEventListener('blur', function() {
        const error = validateName(this.value, 'Last Name');
        const errorElement = document.getElementById('lastNameError');
        if (error) {
            showError(this, errorElement, error);
        } else {
            clearError(this, errorElement);
        }
    });
    
    firstNameInput.addEventListener('blur', function() {
        const error = validateName(this.value, 'First Name');
        const errorElement = document.getElementById('firstNameError');
        if (error) {
            showError(this, errorElement, error);
        } else {
            clearError(this, errorElement);
        }
    });
    
    emailInput.addEventListener('blur', function() {
        const error = validateEmail(this.value);
        const errorElement = document.getElementById('emailError');
        if (error) {
            showError(this, errorElement, error);
        } else {
            clearError(this, errorElement);
        }
    });
    
    //clear error when typing
    studentIdInput.addEventListener('input', function() {
        const errorElement = document.getElementById('studentIdError');
        clearError(this, errorElement);
    });
    
    lastNameInput.addEventListener('input', function() {
        const errorElement = document.getElementById('lastNameError');
        clearError(this, errorElement);
    });
    
    firstNameInput.addEventListener('input', function() {
        const errorElement = document.getElementById('firstNameError');
        clearError(this, errorElement);
    });
    
    emailInput.addEventListener('input', function() {
        const errorElement = document.getElementById('emailError');
        clearError(this, errorElement);
    });
    
    //hndle form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let hasErrors = false;
        
        // Validate all fields
        const studentIdError = validateStudentId(studentIdInput.value);
        const lastNameError = validateName(lastNameInput.value, 'Last Name');
        const firstNameError = validateName(firstNameInput.value, 'First Name');
        const emailError = validateEmail(emailInput.value);
        
        //display errors
        if (studentIdError) {
            showError(studentIdInput, document.getElementById('studentIdError'), studentIdError);
            hasErrors = true;
        }
        
        if (lastNameError) {
            showError(lastNameInput, document.getElementById('lastNameError'), lastNameError);
            hasErrors = true;
        }
        
        if (firstNameError) {
            showError(firstNameInput, document.getElementById('firstNameError'), firstNameError);
            hasErrors = true;
        }
        
        if (emailError) {
            showError(emailInput, document.getElementById('emailError'), emailError);
            hasErrors = true;
        }
        
        //submit 
        if (!hasErrors) {
            alert('Student added successfully!');
            form.reset();
        }
    });
});

