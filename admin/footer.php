            </main>
        </div>
    </div>

    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            const timeEl = document.getElementById('current-time');
            if (timeEl) {
                timeEl.textContent = now.toLocaleTimeString();
            }
        }
        updateTime();
        setInterval(updateTime, 1000);

        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            if (sidebar) {
                sidebar.classList.toggle('open');
                sidebar.classList.toggle('-translate-x-full');
            }
        }
    </script>
</body>
</html>