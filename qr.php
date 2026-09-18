<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <title>LifetimePay QR টিকিট</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.9/minified/html5-qrcode.min.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri&display=swap" rel="stylesheet">

<style>
*{
  box-sizing:border-box;
  font-family:'Hind Siliguri',sans-serif;
}

body{
  margin:0;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(135deg,#2563eb,#06b6d4);
  padding:20px;
}

/* Card */
.card{
  background:#fff;
  width:100%;
  max-width:430px;
  padding:30px;
  border-radius:24px;
  box-shadow:0 20px 60px rgba(0,0,0,.25);
  text-align:center;
  position:relative;
}

/* Banner */
.ticket-banner{
  position:absolute;
  top:-15px;
  right:-15px;
  background:#16a34a;
  color:#fff;
  padding:6px 14px;
  font-size:12px;
  border-radius:10px;
  transform:rotate(15deg);
  font-weight:bold;
}

/* Heading */
h2{
  color:#2563eb;
  margin-bottom:18px;
}

/* Input */
input{
  width:100%;
  padding:14px;
  border-radius:12px;
  border:2px solid #e2e8f0;
  font-size:15px;
  margin-bottom:15px;
  outline:none;
}

input:focus{
  border-color:#2563eb;
}

/* Buttons */
.buttons{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  justify-content:center;
}

button{
  flex:1;
  padding:12px;
  border:none;
  border-radius:12px;
  font-weight:bold;
  cursor:pointer;
  transition:.3s;
}

.btn-primary{ background:#2563eb; color:#fff; }
.btn-success{ background:#16a34a; color:#fff; }
.btn-warning{ background:#f59e0b; }

button:hover{
  transform:translateY(-2px);
  opacity:.9;
}

/* QR Box */
#qrcode{
  margin:25px auto;
  width:250px;
  height:250px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#f1f5f9;
  border-radius:18px;
}

#expiry{
  font-size:14px;
  color:#64748b;
  min-height:24px;
}

#reader{
  margin-top:20px;
  display:none;
  border-radius:12px;
  overflow:hidden;
}

footer{
  margin-top:20px;
  font-size:13px;
  color:#64748b;
}
</style>
</head>


<body>

<div class="card">

  <div class="ticket-banner">🎟 Official Ticket</div>

  <h2>LifetimePay QR তৈরি করুন</h2>

  <input type="text" id="mobile" placeholder="মোবাইল / Payment Link দিন" />

  <div class="buttons">
    <button class="btn-primary" onclick="generateQR()">QR তৈরি</button>
    <button class="btn-success" onclick="downloadQR()">ডাউনলোড</button>
    <button class="btn-warning" onclick="startScanner()">স্ক্যান</button>
  </div>

  <div id="qrcode"></div>
  <div id="expiry"></div>
  <div id="reader"></div>

  <footer>
    © 2026 <a href="https://lifetimepay.com" target="_blank">LifetimePay.com</a>
  </footer>
</div>



<script>
let qrTimeout;

/* ===== Generate QR ===== */
function generateQR(){

  const input = document.getElementById("mobile").value.trim();
  const box = document.getElementById("qrcode");
  const expiry = document.getElementById("expiry");

  box.innerHTML="";
  expiry.innerText="";

  let qrValue="";

  /* LifetimePay Link Support */
  if(input.startsWith("https://lifetimepay.com/paymentlink/")){
    qrValue=input;
  }

  /* Mobile Number Support */
  else if(/^01\d{9}$/.test(input)){
    const payload={
      uid:input,
      brand_id:"1"
    };
    const encoded=btoa(JSON.stringify(payload));
    qrValue=`https://lifetimepay.com/paymentlink/${encoded}`;
  }

  else{
    alert("সঠিক মোবাইল নম্বর বা লিংক দিন");
    return;
  }

  QRCode.toCanvas(qrValue,{width:250},(err,canvas)=>{
    canvas.id="qrCanvas";
    box.appendChild(canvas);
  });

  expiry.innerText="⏳ ৩০ সেকেন্ড পর QR মেয়াদ শেষ হবে";

  clearTimeout(qrTimeout);
  qrTimeout=setTimeout(()=>{
    box.innerHTML="";
    expiry.innerText="❌ QR মেয়াদ শেষ";
  },30000);
}


/* ===== Download ===== */
function downloadQR(){
  const canvas=document.getElementById("qrCanvas");

  if(!canvas){
    alert("আগে QR তৈরি করুন");
    return;
  }

  const link=document.createElement("a");
  link.download="lifetimepay_qr.png";
  link.href=canvas.toDataURL();
  link.click();
}


/* ===== Scanner ===== */
function startScanner(){

  const reader=document.getElementById("reader");
  reader.style.display="block";

  const html5QrCode=new Html5Qrcode("reader");

  html5QrCode.start(
    { facingMode:"environment" },
    { fps:10, qrbox:250 },

    message=>{
      alert("✅ স্ক্যান সম্পন্ন:\n"+message);
      html5QrCode.stop();
      reader.style.display="none";
    },

    err=>{}
  );
}
</script>

</body>
</html>