import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import VisitTypeConst from "./constants";
import dayjs from "dayjs";

document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const calendar = new Calendar(calendarEl, {
        themeSystem: "bootstrap5",
        plugins: [interactionPlugin, dayGridPlugin],
        locale: "ja",
        initialView: "dayGridMonth",
        eventColor: "blue",
        headerToolbar: {
            center: "backEventButton",
        },
        customButtons: {
            backEventButton: {
                text: "戻る",
                click: function () {
                    window.location.href = "/people";
                },
            },
        },
        dayMaxEvents: true,
        selectable: true,
        selectMirror: true,
        dateClick: function(info) {
            const arrivalDate = document.getElementById("arrival-date");
            if (arrivalDate) {
                const formattedDate = dayjs(info.date).format("YYYY-MM-DD[T]HH:mm");
                arrivalDate.value = formattedDate;
                window.selectedDate = info.date;
            }
            openModal();
        },
        editable: false,
        navLinks: false,
        events: function(info, successCallback, failureCallback) {
            axios.get("/calendar/index_scheduled_visit")
                .then((response) => {
                    const events = response.data.contents.map(schedule => {
                        return {
                            id: schedule.id,
                            title: `${schedule.person_name}\n${schedule.pick_up === '必要' ? 
                                `迎え: ${schedule.pick_up_time ? dayjs(schedule.pick_up_time).format('H時mm分') : '時間未定'}` : 
                                '迎え: 不要'}`,
                            start: schedule.arrival_datetime,
                            end: schedule.exit_datetime || schedule.arrival_datetime,
                            backgroundColor: 'transparent',
                            borderColor: 'transparent',
                            textColor: '#000000',
                            className: 'font-bold'
                        };
                    });
                    successCallback(events);
                })
                .catch((error) => {
                    console.error("Error fetching events:", error);
                    failureCallback(error);
                });
        },
    });

    window.closeModal = closeModal;
    const closeButton = document.querySelector(".modal-close-btn");
    if (closeButton) {
        closeButton.addEventListener("click", closeModal);
    }

    // 登録ボタンのイベントリスナー設定
    registerSchedule();

    // 登録モーダルで表示する利用者名の選択肢を取得
    const selectPeople = document.getElementById("selectPeople");
if (selectPeople) {
    axios
        .get("/calendar/index_person")
        .then((response) => {
            if (response.status === 204) {
                return;
            }
            response.data.contents.forEach((person) => {
                const option = document.createElement("option");
                option.value = person.id;
                option.textContent = `${person.last_name} ${person.first_name}`;
                selectPeople.appendChild(option);
            });
        })
        .catch((error) => {
            console.error("Error fetching people:", error);
            alert("データの取得に失敗しました。");
        });
} else {
    console.error("Element #selectPeople not found");
}


    // 登録モーダルで表示する訪問タイプの選択肢を取得
    const selectVisitType = document.getElementById("selectVisitType");
    // axios
    //     .get("/calendar/index_visit_type")
    //     .then((response) => {
    //         response.data.contents.forEach((type) => {
    //             const option = document.createElement("option");
    //             option.value = type.id;
    //             option.textContent = VisitTypeConst.VISIT_TYPE_JA[type.type];
    //             selectVisitType.appendChild(option);
    //         });
    //     })
    //     .catch((error) => {
    //         console.error("Error fetching people:", error);
    //         alert("データの取得に失敗しました。");
    //     });
        if (selectVisitType) {
            // デフォルトで日帰り（ID: 1）を設定
            selectVisitType.value = "1";
        }
    // フォームの送信
    let originalData = {};
    function registerSchedule() {
    document
        .getElementById("eventForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();
            
            const peopleId = document.getElementById("selectPeople")?.value;
            const visitTypeId = document.getElementById("selectVisitType")?.value;
            const arrivalDate = document.getElementById("arrival-date").value;
            
            // 日付が空の場合のチェック
            if (!arrivalDate) {
                alert('来訪予定日を入力してください');
                return;
            }
            
            // 日付と時間を正しい形式に変換
            const formattedDateTime = dayjs(arrivalDate).format('YYYY-MM-DD HH:mm:ss');
            
            // 日付が正しく変換されたかチェック
            if (formattedDateTime === 'Invalid Date') {
                alert('日付の形式が正しくありません');
                return;
            }
            
            const dataToSend = {
                people_id: peopleId,
                // visit_type_id: visitTypeId,
                visit_type_id: 1, // 日帰りのIDを固定で設定
                arrival_datetime: formattedDateTime,
                exit_datetime: null,
                pick_up: document.querySelector('input[name="pick_up"]:checked')?.value || null,
                drop_off: document.querySelector('input[name="drop_off"]:checked')?.value || null,
                pick_up_time: document.getElementById("pick_up_time")?.value ? 
                dayjs(arrivalDate.split('T')[0] + ' ' + document.getElementById("pick_up_time").value + ':00').format('YYYY-MM-DD HH:mm:ss') : null,
                drop_off_time: null,
                pick_up_staff: document.getElementById("pick_up_staff")?.value || null,
                drop_off_staff: document.getElementById("drop_off_staff")?.value || null,
                pick_up_bus: document.getElementById("pick_up_bus")?.value || null,
                drop_off_bus: document.getElementById("drop_off_bus")?.value || null,
                notes: null
            };

            // デバッグ用のログ
            console.log('送信する日時:', formattedDateTime);
            
            axios.post("/calendar/register", dataToSend, {
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
            .then(() => {
                alert("登録しました");
                calendar.refetchEvents();
                closeModal();
            })
            .catch((error) => {
                console.error('Error:', error);
                if (error.response?.data?.errors) {
                    const errorMessages = Object.values(error.response.data.errors).flat();
                    alert(errorMessages.join('\n'));
                } else {
                    alert('登録中にエラーが発生しました。入力内容を確認してください。');
                }
            });
        });
}

    
    calendar.setOption("eventClick", function (info) {
        axios
            .get("/calendar/scheduled_visit_detail/", {
                params: {
                    scheduled_visit_id: info.event.id,
                },
            })
            .then((response) => {
                const schedule = response.data.contents;
                // オプション選択モーダルを表示
                document
                    .getElementById("optionModal")
                    .classList.remove("hidden");
                // 編集ボタンのイベントハンドラ
                document.getElementById("editButton").onclick = function () {
                    const eventData = {
                        person_id: schedule.people_id,
                        visit_type_id: schedule.visit_type_id,
                        start: info.event.start.toISOString(),
                        end: info.event.end.toISOString(),
                    };
                    // 編集モーダルを開く
                    openModal(true, eventData);
                    document
                        .getElementById("optionModal")
                        .classList.add("hidden");
                };

                // 削除ボタンのイベントハンドラ
                document.getElementById("deleteButton").onclick = function () {
                    openDeleteModal();
                    document
                        .getElementById("optionModal")
                        .classList.add("hidden");
                    document.getElementById("confirmDelete").onclick =
                        function () {
                            deleteEvent(info.event.id);
                            document
                                .getElementById("optionModal")
                                .classList.add("hidden");
                        };
                };
            })
            .catch((error) => {
                console.error("Error fetching event details:", error);
                alert("イベントデータの取得に失敗しました。");
            });
    });

    // 削除メソッド
    function deleteEvent(eventId) {
        axios
            .post(
                "/calendar/delete",
                {
                    schedule_id: eventId,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                }
            )
            .then(() => {
                alert("予定が削除されました");
                closeDeleteModal();
                calendar.refetchEvents();
            })
            .catch((error) => {
                console.error("削除エラー:", error);
                alert("削除に失敗しました");
            });
    }

    calendar.render();
});

// スクロールバーの幅を計算
function getScrollbarWidth() {
    return window.innerWidth - document.documentElement.clientWidth;
}

// 予定登録・編集用のモーダル開く
function openModal(edit = false, eventData = null) {
    const scrollbarWidth = getScrollbarWidth();
    document.body.style.overflow = "hidden";
    document.body.style.paddingRight = `${scrollbarWidth}px`; // スクロールバーの幅を補正
    const modal = document.getElementById("modalBackdrop");
    const modalTitle = document.getElementById("modalTitle");
    const submitButton = document.getElementById("submitButton");
    if (edit) {
        modalTitle.textContent = "予定を編集";
        submitButton.textContent = "編集する";
        // ここでフォームに既存のデータをセットする
        document.getElementById("selectPeople").value = eventData.person_id;
        document.getElementById("selectVisitType").value =
            eventData.visit_type_id;
        document.getElementById("arrival-date").value = dayjs(
            eventData.start
        ).format("YYYY-MM-DD");
        document.getElementById("arrival-time").value = dayjs(
            eventData.start
        ).format("HH:mm");
        document.getElementById("exit-date").value = dayjs(
            eventData.end
        ).format("YYYY-MM-DD");
        document.getElementById("exit-time").value = dayjs(
            eventData.end
        ).format("HH:mm");

        // 送迎の要否のラジオボタンをセット
        // const transportYes = document.getElementById("transport-yes");
        // const transportNo = document.getElementById("transport-no");

        // if (transportYes && transportNo) {
        //     if (eventData.transport === 'あり') {
        //         transportYes.checked = true;
        //         transportNo.checked = false;
        //     } else {
        //         transportYes.checked = false;
        //         transportNo.checked = true;
        //     }
        // } else {
        //     console.warn("送迎の要否のラジオボタンが見つかりません");
        // }
    } else {
        modalTitle.textContent = "来訪日登録";
        submitButton.textContent = "登録";
        // フォームリセットを条件付きで行う
        if (!window.selectedDate) {
            document.getElementById("eventForm").reset();
        }
    }

    modal.classList.remove("hidden");
}

// 登録モーダルを閉じる関数
function closeModal() {
    document.body.style.overflow = "";
    document.body.style.paddingRight = ""; // 補正を解除
    const modal = document.getElementById("modalBackdrop");
    modal.classList.add("hidden");
}

// 削除モーダル
function openDeleteModal() {
    document.getElementById("deleteModal").classList.remove("hidden");
}
const cancelDelete = document.getElementById("cancelDelete");
if (cancelDelete) {
    document
        .getElementById("cancelDelete")
        .addEventListener("click", function () {
            document.getElementById("deleteModal").classList.add("hidden");
        });
}
const cancelOption = document.getElementById("cancelOption");
if (cancelOption) {
    document
        .getElementById("cancelOption")
        .addEventListener("click", function () {
            document.getElementById("optionModal").classList.add("hidden");
        });
}

function closeDeleteModal() {
    document.body.style.overflow = "";
    document.body.style.paddingRight = ""; // 補正を解除
    const modal = document.getElementById("deleteModal");
    modal.classList.add("hidden");
}

// closeModalの設定の後に追加
const cancelButton = document.getElementById("cancelButton");
if (cancelButton) {
    cancelButton.addEventListener("click", function() {
        closeModal();
    });
}