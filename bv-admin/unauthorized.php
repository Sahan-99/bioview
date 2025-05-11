<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffeaa7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        .unauth-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.8s ease-out;
        }
        .unauth-container i {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
            animation: pulseIcon 1.5s infinite;
        }
        .unauth-container h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 20px 0;
        }
        .unauth-container p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .btn-go-back {
            background-color: #dc3545;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-go-back:hover {
            background-color: #c82333;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .error-code {
            font-size: 4rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 10px;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseIcon {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        @media (max-width: 576px) {
            .unauth-container {
                padding: 20px;
                margin: 15px;
            }
            .error-code { font-size: 3rem; }
            .unauth-container h1 { font-size: 1.8rem; }
            .unauth-container p { font-size: 1rem; }
            .unauth-container i { font-size: 4rem; }
        }
    </style>
</head>
<body>
    <div class="unauth-container">
        <i class="fas fa-exclamation-triangle"></i>
        <div class="error-code">403</div>
        <h1>Access Denied</h1>
        <p>You do not have permission to view this page.</p>
        <button class="btn btn-go-back" onclick="goBack()">Go Back</button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>