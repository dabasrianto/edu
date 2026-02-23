<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            text-align: center;
        }
        .container {
            width: 100%;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }
        .border-pattern {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 10px double #1e3a8a; /* Blue-900 */
        }
        .header {
            margin-top: 60px;
        }
        .logo {
            font-size: 40px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 20px;
        }
        .subtitle {
            font-size: 18px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .title {
            font-size: 50px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 20px 0;
            font-family: 'Times New Roman', serif;
        }
        .content {
            margin-top: 30px;
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .name {
            font-size: 32px;
            font-weight: bold;
            color: #000;
            margin: 20px 0;
            border-bottom: 2px solid #ccc;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 400px;
        }
        .course-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 10px 0;
        }
        .footer {
            margin-top: 80px;
            display: table;
            width: 100%;
            padding: 0 100px;
        }
        .signature {
            display: table-cell;
            text-align: center;
            vertical-align: top;
        }
        .line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
            margin-top: 50px;
        }
        .cert-id {
            position: absolute;
            bottom: 30px;
            right: 40px;
            font-size: 10px;
            color: #aaa;
        }
    </style>
    @if(isset($bgImage) && $bgImage)
    <style>
        body {
            background-image: url('{{ $bgImage }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .border-pattern { display: none; }
        .logo { color: #fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.5); } 
        /* Assuming custom background might be dark or colorful, keep text readable or just let user design handle it. 
           But usually text needs to be black on white paper. 
           If it's a certificate background, the middle is usually white/light. */
    </style>
    @endif
</head>
<body>
    @if(!isset($bgImage) || !$bgImage)
    <div class="border-pattern"></div>
    @endif
    <div class="container">
        <div class="header">
            <div class="logo">Edu HSI</div>
            <div class="subtitle">Sertifikat Kelulusan</div>
        </div>

        <div class="content">
            <p>Diberikan kepada:</p>
            <div class="name">{{ $user->name }}</div>
            
            <p>Atas keberhasilannya menyelesaikan dan lulus ujian pada materi:</p>
            <div class="course-name">{{ $quiz->title }}</div>
            
            <p>Dengan Nilai Akhir:</p>
            <h2 style="color: #059669; font-size: 28px;">{{ $score }}</h2>
            
            <p><i>"Semoga ilmu yang didapatkan bermanfaat bagi diri sendiri dan umat."</i></p>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Date: {{ $date }}</p>
                <div class="title" style="font-size: 0;"></div> <!-- Spacer -->
                <br><br>
            </div>
            <div class="signature">
                <br><br><br>
                <div class="line"></div>
                <p><strong>Admin Edu HSI</strong></p>
            </div>
        </div>

        <div class="cert-id">
            ID: {{ $certificate_id }}
        </div>
    </div>
</body>
</html>
