const employeeId = window.Laravel.employeeId;

Echo.private(`App.Models.User.${employeeId}`).notification((notification) => {
    const badge = document.getElementById("notification-count");
    let count = parseInt(badge.innerText || "0");
    badge.innerText = ++count;

    document.querySelector("#notification-list small")?.remove();

    const html = `
        <a class="dropdown-item" href="${
            notification.data.link ?? "javascript:"
        }">
            <div class="d-flex align-items-center">
                <div class="notify text-primary">
                    <ion-icon name="${
                        notification.data.icon ?? "notifications-outline"
                    }"></ion-icon>
                </div>
                <div class="flex-grow-1">
                    <h6 class="msg-name">
                        ${notification.message}
                        <span class="msg-time float-end">Vừa xong</span>
                    </h6>
                    <p class="msg-info">
                        ${
                            notification.data.body
                                ?.split(" ")
                                .slice(0, 7)
                                .join(" ") ?? ""
                        }
                    </p>
                </div>
            </div>
        </a>
    `;

    // Thêm lên đầu danh sách
    const list = document.getElementById("notification-list");
    list.insertAdjacentHTML("afterbegin", html);

    datgin.info(`${notification.message}: ${notification.data.body}`);
});
