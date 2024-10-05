async function handleUpdateUserList() {
  try {
    const userList = document.querySelector('.list-group');
    const baseUrl = userList.dataset.baseUrl;
    const isAdmin = userList.dataset.admin === 'true';

    const lastUserLi = userList.lastElementChild;
    const lastUserId = lastUserLi ? lastUserLi.dataset.id : 0;
    const response = await fetch(`${baseUrl}/users/indexRefresh/?maxId=${lastUserId}`);

    if (response.ok) {
      const users = await response.json();


      if (users.length > 0) {
        users.forEach(user => {
          const li = document.createElement('li');
          li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
          li.dataset.id = user.id;
          li.innerHTML = `
            <div>
              <strong>${user.username} ${user.lastname}</strong><br>
              <small>День рождения: ${user.birthday ? user.birthday : 'Не указана'}</small>
            </div>
          `;

          if (isAdmin) {
            const actionButtons = `
              <div>
                <form method="GET" action="${baseUrl}/users/delete/" style="display:inline;">
                  <input type="hidden" name="id" value="${user.id}">
                  <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                </form>
                <form method="GET" action="${baseUrl}/users/edit/" style="display:inline;">
                  <input type="hidden" name="id" value="${user.id}">
                  <button type="submit" class="btn btn-warning btn-sm">Изменить</button>
                </form>
              </div>
            `;
            li.innerHTML += actionButtons;
          }
          userList.appendChild(li);
        });
      }
    }
  } catch (error) {
    console.error('Ошибка при получении пользователей:', error);
  }
}

function startUsersUpdating() {
  handleUpdateUserList();
  updateInterval = setInterval(handleUpdateUserList, 10000);
}

function stopUsersUpdating() {
  clearInterval(updateInterval);
}

async function handleUserDelete(event) {
  if (event.target.matches('.btn-danger')) {
    event.preventDefault();

    const userList = document.querySelector('.list-group');
    const form = event.target.closest('form');
    const userId = form.querySelector('input[name="id"]').value;
    const baseUrl = userList.dataset.baseUrl;

    try {
      const response = await fetch(`${baseUrl}/users/delete/?id=${userId}`, {
        method: 'GET',
      });

      if (response.ok) {
        const li = form.closest('li');
        li.remove();
      }
    } catch (error) {
      console.error('Ошибка при удалении пользователя:', error);
    }
  }
}
