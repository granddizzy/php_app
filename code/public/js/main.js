async function updateUserList() {
  try {
    const userList = document.querySelector('.list-group');
    const baseUrl = userList.dataset.baseUrl;

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
          userList.appendChild(li);
        });
      }
    }
  } catch (error) {
    console.error('Ошибка при получении пользователей:', error);
  }
}

function startUsersUpdating() {
  updateUserList();
  updateInterval = setInterval(updateUserList, 10000);
}

function stopUsersUpdating() {
  clearInterval(updateInterval);
}