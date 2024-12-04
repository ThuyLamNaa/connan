document.getElementById('myForm').addEventListener('submit', function (event) {
    // Ngăn chặn gửi form mặc định
    event.preventDefault();

    // Lấy giá trị từ các trường
    const product_name = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    // Các phần tử để hiển thị lỗi
    const usernameError = document.getElementById('usernameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    // Đặt lại thông báo lỗi
    productnameError.textContent = '';
    emailError.textContent = '';
    passwordError.textContent = '';

    let isValid = true;

    // Kiểm tra tên người dùng
    if (product_name === '') {
        productnameError.textContent = 'Vui lòng nhập tên sản phẩm.';
        isValid = false;
    }
    // Kiểm tra email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        emailError.textContent = 'Vui lòng nhập email.';
        isValid = false;
    } else if (!emailRegex.test(email)) {
        emailError.textContent = 'Địa chỉ email không hợp lệ.';
        isValid = false;
    }

    // Kiểm tra mật khẩu
    if (password === '') {
        passwordError.textContent = 'Vui lòng nhập mật khẩu.';
        isValid = false;
    } else if (password.length < 6) {
        passwordError.textContent = 'Mật khẩu phải có ít nhất 6 ký tự.';
        isValid = false;
    }

    // Nếu tất cả hợp lệ, gửi form
    if (isValid) {
        alert('Form hợp lệ! Đang gửi...');
        this.submit();
    }
});
