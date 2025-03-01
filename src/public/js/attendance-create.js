function updateDateTime() {
    const now = new Date();

    // 日本語の曜日を取得
    const dayNames = ['日', '月', '火', '水', '木', '金', '土'];
    const year = now.getFullYear();
    const month = now.getMonth() + 1;
    const date = now.getDate();
    const day = dayNames[now.getDay()];

    // 日付を表示
    document.getElementById('current-date').textContent = `${year}年${month}月${date}日 (${day})`;

    // 時刻を表示
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');

    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
}

updateDateTime(); // 初回実行
setInterval(updateDateTime, 1000); // 1秒ごとに更新
