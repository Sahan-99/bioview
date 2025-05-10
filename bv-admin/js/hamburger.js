// Hamburger menu and close icon toggle
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        backdrop.classList.toggle('active');
        mainContent.classList.toggle('sidebar-hidden');
    }

    hamburger.addEventListener('click', toggleSidebar);

    if (closeSidebar) {
        closeSidebar.addEventListener('click', toggleSidebar);
    }

    backdrop.addEventListener('click', toggleSidebar);

    // Auto Logout After 1 Minute of Inactivity
    let logoutTimer;

    function resetLogoutTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(() => {
            window.location.href = 'lock_screen.php';
        }, 600000); // 1 minute = 60000 ms
    }

    // Reset timer on these activities
    window.onload = resetLogoutTimer;
    document.onmousemove = resetLogoutTimer;
    document.onkeypress = resetLogoutTimer;
    document.onclick = resetLogoutTimer;
    document.onscroll = resetLogoutTimer;
});