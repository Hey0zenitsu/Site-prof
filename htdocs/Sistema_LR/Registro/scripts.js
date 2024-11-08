document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita o envio padrão do formulário
    let username = document.getElementById('username').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();
    let date = document.getElementById('date').value.trim();
    const messageElement = document.getElementById('message');

    // Validação simples
    if (username === '' || email === '' || password === '' || date === '') {
        messageElement.style.color = 'red';
        messageElement.textContent = 'Por favor, preencha todos os campos.';
        return;
    }

    // Remover caracteres perigosos de SQL (aspas simples, aspas duplas, barra invertida, ponto-e-vírgula, #, /, *)
    const forbiddenCharsPattern = /['"\\;#/*]/g;

    if (forbiddenCharsPattern.test(username)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'O nome de usuário contém caracteres não permitidos: aspas simples, aspas duplas, barra invertida, ponto-e-vírgula, #, / ou *.';
        return;
    }
    
    if (forbiddenCharsPattern.test(email)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'O email contém caracteres não permitidos: aspas simples, aspas duplas, barra invertida, ponto-e-vírgula, #, / ou *.';
        return;
    }
    
    if (forbiddenCharsPattern.test(password)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'A senha contém caracteres não permitidos: aspas simples, aspas duplas, barra invertida, ponto-e-vírgula, #, / ou *.';
        return;
    }

    // Validação de username (letras, números, _, -)
    const usernamePattern = /^[a-zA-Z0-9_-]+$/;
    if (!usernamePattern.test(username)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'O nome de usuário pode conter apenas letras, números, _ e - .';
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
    if (password.length < 6) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'A senha deve ter pelo menos 6 caracteres.';
        return;
    }

    // Validação de data (YYYY-MM-DD)
    const datePattern = /^\d{4}-\d{2}-\d{2}$/;
    if (!datePattern.test(date)) {
        messageElement.style.color = 'red';
        messageElement.textContent = 'Por favor, insira uma data válida no formato AAAA-MM-DD.';
        return;
    }

    // Enviar dados para o servidor
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'register.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText.trim());
                if (response.status === 'success') {
                    messageElement.style.color = 'green';
                    messageElement.textContent = 'Cadastro concluído';
                    // Opcional: redirecionar após sucesso
                    window.location.href = '../Login/Login.html';
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

    xhr.send(
        'username=' + encodeURIComponent(username) +
        '&email=' + encodeURIComponent(email) +
        '&password=' + encodeURIComponent(password) +
        '&date=' + encodeURIComponent(date)
    );
});
