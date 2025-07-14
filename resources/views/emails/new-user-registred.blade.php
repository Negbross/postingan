<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi User Baru</title>
</head>
<body style="font-family: sans-serif; color: #333;">
<h2>Halo Admin,</h2>
<p>Seorang pengguna baru telah berhasil mendaftar di website Anda.</p>
<p>Berikut adalah detailnya:</p>
<ul>
    <li><strong>Nama:</strong> {{ $user->name }}</li>
    <li><strong>Email:</strong> {{ $user->email }}</li>
    <li><strong>Waktu Daftar:</strong> {{ $user->created_at->format('d M Y, H:i') }}</li>
</ul>
<p>Terima kasih.</p>
</body>
</html>
