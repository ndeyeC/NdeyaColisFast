
 const firebaseConfig = {
    apiKey: "AIzaSyAgiNsg2rKxdIP8FTyACsz6839HWmbnX0Q",
    authDomain: "colisfast-fbe98.firebaseapp.com",
    projectId: "colisfast-fbe98",
    storageBucket: "colisfast-fbe98.firebasestorage.app",
    messagingSenderId: "801131680216",
    appId: "1:801131680216:web:86a40c556e713ef238e2ad",
    measurementId: "G-VQ5HEZFVH8"
  };


firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();


messaging.requestPermission().then(() => {
    return messaging.getToken();
}).then(token => {
    console.log("Token FCM récupéré :", token);

    fetch('/user/fcm-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ fcm_token: token })
    });
}).catch(err => {
    console.error("Erreur récupération du token FCM", err);
});
