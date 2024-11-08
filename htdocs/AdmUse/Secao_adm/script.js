document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita o envio padrão do formulário
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const messageElement = document.getElementById('message');

    // Validação simples
    if (email === '' || password === '') {
        messageElement.style.color = 'red';
        messageElement.textContent = 'Por favor, preencha todos os campos.';
        return;
    }

    // Validação de email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'Por favor, insira um email válido.';
        return;
    }

    // Validação de senha (deve ter pelo menos 6 caracteres)
    const passwordPattern = /^[a-zA-Z0-9!@#$%^&*()_+={}\[\]|\\:;"'<>,.?/-]{6,}$/;
    if (!passwordPattern.test(password)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'A senha deve ter pelo menos 6 caracteres e não conter caracteres especiais indesejados.';
        return;
    }

    // Enviar dados para o servidor
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'validacao.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText.trim());
                if (response.status === 'success') {
                    messageElement.style.color = 'green';
                    messageElement.textContent = 'Login bem-sucedido';
                    // Redirecionar após sucesso
                    window.location.href = '../estoque/estoque.php';
                } else {
                    messageElement.style.color = 'red';
                    messageElement.textContent = response.message;
                }
            } else {
                messageElement.style.color = 'red';
                messageElement.textContent = 'Erro no servidor. Por favor, tente novamente mais tarde.';
            }
        }
    };
    xhr.onerror = function() {
        messageElement.style.color = 'red';
        messageElement.textContent = 'Erro na conexão. Por favor, tente novamente mais tarde.';
    };

    xhr.send('email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password));
});
