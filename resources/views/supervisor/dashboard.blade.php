<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supervisor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/supervisor/dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
        </header>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Selamat datang <strong>Mawar</strong> di <span class="system-name">Sistem Persuratan!</span> Anda login sebagai <span class="role-badge">Supervisor</span></p>
        </div>

        <!-- Overview Section -->
        <div class="overview-container">
            <h3>Overview</h3>
            <div class="overview-cards">
                <div class="overview-card">
                    <h4>MEMO</h4>
                    <p><strong>4</strong> Memo</p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <p><strong>7</strong> Risalah Rapat</p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <p><strong>15</strong> Undangan Rapat</p>
                </div>
            </div>
        </div>

        <div class="things-to-do-container">
            <!-- History hari ini -->
            <h3>Today</h3>
            <div class="things-to-do-list">
                <form action="#">
                    <div class="icon1">
                        <img src="/img/dashboard/memoy.png" alt="Memo Icon">
                    </div>
                </form>
                <div class="content">
                    <h4>Memo Ekspor</h4>
                    <p>5:12 pm • <span>Approve</span></p>
                </div>
            </div>
            <div class="things-to-do-list">   
                <form action="#">
                    <div class="icon2">
                        <img src="/img/dashboard/surat.png" alt="Undangan Icon">
                    </div>
                </form>
                <div class="content">
                    <h4>Surat Undangan Kebijakan</h4>
                    <p>5:12 pm • <span>Approve</span></p>
                </div>
            </div>
            <div class="things-to-do-list">
                <form action="#">
                    <div class="icon3">
                        <img src="/img/dashboard/vectory.png" alt="Risalah Icon">
                    </div>
                </form>
                <div class="content">
                    <h4>Risalah Rapat HUT</h4>
                    <p>5:12 pm • <span>Reject</span></p>
                </div>
            </div>
        </div>
            
        <!-- History kemarin -->
        <div class="things-to-do-container">
            <h3>Friday, 28 Oct</h3>
            <div class="things-to-do-list">
                <form action="#">
                    <div class="icon1">
                        <img src="/img/dashboard/memoy.png" alt="Memo Icon">
                    </div>
                </form>
                <div class="content">
                    <h4>Memo Penyusunan</h4>
                    <p>5:12 pm • <span>Reject</span></p>
                </div>
            </div>
            <div class="things-to-do-list">   
                <form action="#">
                    <div class="icon2">
                        <img src="/img/dashboard/surat.png" alt="Undangan Icon">
                    </div>
                </form>
                <div class="content">
                    <h4>Surat Undangan Internal Pelaksanaan</h4>
                    <p>5:12 pm • <span>Approve</span></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
