<div id="whatsapp-account" class="account-container"></div>
<style>.account-container{display: flex;flex-direction: column;align-items: center;justify-content: center;width: 100%;height: 100%;}</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const serviceUrl = `{{config('services.whatsapp-notification-service.service_api_url')}}`
    const whatsappSection = document.getElementById('whatsapp-account')

    document.addEventListener("DOMContentLoaded", async function () {
        // process to verify is whatsapp account is already connected
        whatsappSection.innerText = 'Loading...'

        // call the login api
        await login().then((response) => {
            // show qrcode when user session not found
            if (parseInt(response.data.code) === 201) {
                whatsappSection.innerHTML = `
                    <section>
                    <span id="qrcode"></span>
                    <p>This QrCode will expire in 20 second</p>
                    <button onclick="refresh()">REFRESH</button>
                    </section>
                `
                const qrcode = new QRCode(document.getElementById("qrcode"), {
                    width: 256,
                    height: 256,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });

                qrcode.clear()
                qrcode.makeCode(response.data.data.qrcode)
            }

            // show user data when user session found
            if (parseInt(response.data.code) === 200) {
                whatsappSection.innerHTML = `
                    <section>
                    <p>
                        Logged in as ${response.data.data.display_name}, <br>
                        on ${response.data.data.phone_platform},
                        battery status ${response.data.data.phone_battery}
                    </p>
                    <button onclick="logout()">LOGOUT</button>
                    </section>
                `
            }
        })
    })

    function refresh() {
        alert("Refresh page")
        window.location.reload()
    }

    async function login() {
        try {
            const loginUrl = `${serviceUrl}/login`

            return await axios({
                method: 'POST',
                url: loginUrl,
                headers: {
                    'Content-Type': 'application/json'
                }
            })
        } catch (error) {
            alert(`Looks like there was a problem: ${error.message}`);
        }
    }

    async function logout() {
        try {
            const logoutUrl = `${serviceUrl}/logout`

            await axios({
                method: 'POST',
                url: logoutUrl,
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                alert('Logout success')
                window.location.reload()
            })
        } catch (error) {
            alert(`Looks like there was a problem: ${error.message}`);
        }
    }
</script>
