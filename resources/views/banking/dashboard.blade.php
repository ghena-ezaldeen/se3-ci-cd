<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم البنكية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .card-header { background-color: #2c3e50; color: white; font-weight: bold; }
        .btn-primary { background-color: #3498db; border-color: #3498db; }
        .alert { display: none; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-5">النظام البنكي المتقدم (SE3 Project)</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">إيداع (Deposit)</div>
                    <div class="card-body">
                        <form id="depositForm">
                            <div class="mb-3">
                                <label>رقم الحساب (ID)</label>
                                <input type="number" name="account_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>المبلغ</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">تنفيذ الإيداع</button>
                        </form>
                        <div class="alert alert-success" id="depositMsg"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-danger">سحب (Withdraw)</div>
                    <div class="card-body">
                        <form id="withdrawForm">
                            <div class="mb-3">
                                <label>رقم الحساب (ID)</label>
                                <input type="number" name="account_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>المبلغ</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">تنفيذ السحب</button>
                        </form>
                        <div class="alert alert-success" id="withdrawMsg"></div>
                        <div class="alert alert-danger" id="withdrawErr"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary">تحويل (Transfer)</div>
                    <div class="card-body">
                        <form id="transferForm">
                            <div class="mb-3">
                                <label>من حساب (ID)</label>
                                <input type="number" name="from_account_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>إلى حساب (ID)</label>
                                <input type="number" name="to_account_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>المبلغ</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">تحويل الأموال</button>
                        </form>
                        <div class="alert alert-success" id="transferMsg"></div>
                        <div class="alert alert-danger" id="transferErr"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // إعدادات Axios
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // دالة عامة للتعامل مع النماذج
        function handleForm(formId, url, successMsgId, errorMsgId = null) {
            document.getElementById(formId).addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                // إخفاء الرسائل السابقة
                document.getElementById(successMsgId).style.display = 'none';
                if(errorMsgId) document.getElementById(errorMsgId).style.display = 'none';

                axios.post('/api/' + url, data)
                    .then(response => {
                        const alertBox = document.getElementById(successMsgId);
                        alertBox.innerText = response.data.message;
                        alertBox.style.display = 'block';
                        this.reset(); // تصفير النموذج
                    })
                    .catch(error => {
                        if(errorMsgId) {
                            const alertBox = document.getElementById(errorMsgId);
                            alertBox.innerText = error.response.data.error || 'حدث خطأ ما';
                            alertBox.style.display = 'block';
                        }
                        console.error(error);
                    });
            });
        }

        // تفعيل النماذج
        handleForm('depositForm', 'deposit', 'depositMsg');
        handleForm('withdrawForm', 'withdraw', 'withdrawMsg', 'withdrawErr');
        handleForm('transferForm', 'transfer', 'transferMsg', 'transferErr');
    </script>
</body>
</html>
