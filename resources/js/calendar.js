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
        dateClick: function (info) {
            console.log("日付がクリックされました:", info);
            const formattedDate = dayjs(info.date).format("YYYY-MM-DD");
            const arrivalDate = document.getElementById("arrival-date");
            const exitDate = document.getElementById("exit-date");
            if (arrivalDate) {
                arrivalDate.value = formattedDate;
                exitDate.value = formattedDate; // exit-dateも同じ日付に設定
                window.selectedDate = info.date;
            }
            openModal();
        },
        editable: false,
        navLinks: false,
        events: function (info, successCallback, failureCallback) {
            axios
                .get("/calendar/index_scheduled_visit")
                .then((response) => {
                    const events = response.data.contents.map((schedule) => {
                        return {
                            id: schedule.id,
                            title: `${schedule.person_name}\n${
                                schedule.pick_up === "必要"
                                    ? `迎え: ${
                                          schedule.pick_up_time
                                              ? dayjs(
                                                    schedule.pick_up_time
                                                ).format("H時mm分")
                                              : "時間未定"
                                      }`
                                    : "迎え: 不要"
                            }`,
                            start: schedule.arrival_datetime,
                            end:
                                schedule.exit_datetime ||
                                schedule.arrival_datetime,
                            backgroundColor: "transparent",
                            borderColor: "transparent",
                            textColor: "#000000",
                            className: "font-bold",
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
                const visitTypeId =
                    document.getElementById("selectVisitType")?.value;
                const arrivalDate =
                    document.getElementById("arrival-date").value;
                const eventId = this.dataset.eventId;

                // 日付が空の場合のチェック
                if (!arrivalDate) {
                    alert("来訪予定日を入力してください");
                    return;
                }

                // 日付と時間を正しい形式に変換
                const formattedDateTime = dayjs(arrivalDate).format(
                    "YYYY-MM-DD HH:mm:ss"
                );

                // 日付が正しく変換されたかチェック
                if (formattedDateTime === "Invalid Date") {
                    alert("日付の形式が正しくありません");
                    return;
                }

                const dataToSend = {
                    scheduled_visit_id: this.dataset.eventId,
                    people_id: peopleId,
                    // visit_type_id: visitTypeId,
                    visit_type_id: 1, // 日帰りのIDを固定で設定
                    arrival_datetime: formattedDateTime,
                    // exit_datetime: null,
                    exit_datetime: formattedDateTime, //nullでも登録エラーを回避するためformattedDateTimeを設定
                    pick_up:
                        document.querySelector('input[name="pick_up"]:checked')
                            ?.value || null,
                    drop_off:
                        document.querySelector('input[name="drop_off"]:checked')
                            ?.value || null,
                    pick_up_time: document.getElementById("pick_up_time")?.value
                        ? dayjs(
                              arrivalDate.split("T")[0] +
                                  " " +
                                  document.getElementById("pick_up_time")
                                      .value +
                                  ":00"
                          ).format("YYYY-MM-DD HH:mm:ss")
                        : null,
                    drop_off_time: null,
                    pick_up_staff:
                        document.getElementById("pick_up_staff")?.value || null,
                    drop_off_staff:
                        document.getElementById("drop_off_staff")?.value ||
                        null,
                    pick_up_bus:
                        document.getElementById("pick_up_bus")?.value || null,
                    drop_off_bus:
                        document.getElementById("drop_off_bus")?.value || null,
                    notes: document.getElementById("notes")?.value || null, // 追加
                };

                const url = eventId
                    ? `/calendar/edit/${eventId}` // 編集の場合
                    : "/calendar/register"; // 新規登録の場合

                // デバッグ用のログ
                console.log("送信する日時:", formattedDateTime);

                axios
                    .post(url, dataToSend, {
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    })
                    .then((response) => {
                        console.log("Registration success:", response);
                        alert(eventId ? "編集しました" : "登録しました");
                        calendar.refetchEvents();
                        closeModal();
                    })
                    .catch((error) => {
                        console.error("Registration error:", {
                            message: error.message,
                            response: error.response?.data,
                            status: error.response?.status,
                        });

                        if (error.response?.data?.message) {
                            alert(error.response.data.message);
                        } else {
                            alert(
                                "登録中にエラーが発生しました。システム管理者に連絡してください。"
                            );
                        }
                    });
            });
    }

    // calendar.js に追加

    // イベントクリック時のハンドラを修正
    calendar.setOption("eventClick", function (info) {
        console.log("クリックされたイベント:", {
            id: info.event.id,
            title: info.event.title,
            start: info.event.start,
            end: info.event.end,
            extendedProps: info.event.extendedProps,
        });

        axios
            .get("/calendar/scheduled_visit_detail/", {
                params: {
                    scheduled_visit_id: info.event.id,
                },
            })
            .then((response) => {
                console.log(
                    "取得したスケジュールデータ:",
                    response.data.contents
                );
                const schedule = response.data.contents;
                document
                    .getElementById("optionModal")
                    .classList.remove("hidden");

                // 編集ボタンのイベントハンドラ
                document.getElementById("editButton").onclick = function () {
                    const eventData = {
                        people_id: schedule.people_id,
                        visit_type_id: schedule.visit_type_id,
                        start: schedule.arrival_datetime,
                        end: schedule.exit_datetime,
                        pick_up: schedule.pick_up,
                        drop_off: schedule.drop_off,
                        pick_up_time: schedule.pick_up_time,
                        pick_up_staff: schedule.pick_up_staff,
                        drop_off_staff: schedule.drop_off_staff,
                        pick_up_bus: schedule.pick_up_bus,
                        drop_off_bus: schedule.drop_off_bus,
                        notes: schedule.notes,
                        id: schedule.id,
                    };
                    console.log("編集するイベントデータ:", eventData);
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
    const form = document.getElementById("eventForm");
    if (edit) {
        modalTitle.textContent = "予定を編集";
        submitButton.textContent = "編集する";
        form.dataset.eventId = eventData.id;
        // ここでフォームに既存のデータをセットする
        document.getElementById("selectPeople").value = eventData.people_id;
        document.getElementById("selectVisitType").value =
            eventData.visit_type_id;
        document.getElementById("arrival-date").value = dayjs(
            eventData.start
        ).format("YYYY-MM-DD");
        document.getElementById("exit-date").value = dayjs(
            eventData.end
        ).format("YYYY-MM-DD");
        document.getElementById("notes").value = eventData.notes || "";
        const pickUpRadios = document.querySelectorAll('input[name="pick_up"]');
        pickUpRadios.forEach((radio) => {
            if (radio.value === eventData.pick_up) radio.checked = true;
        });

        const dropOffRadios = document.querySelectorAll(
            'input[name="drop_off"]'
        );
        dropOffRadios.forEach((radio) => {
            if (radio.value === eventData.drop_off) radio.checked = true;

            document.getElementById("pick_up_time").value =
                eventData.pick_up_time
                    ? dayjs(eventData.pick_up_time).format("HH:mm")
                    : "";
            document.getElementById("pick_up_staff").value =
                eventData.pick_up_staff || "";
            document.getElementById("drop_off_staff").value =
                eventData.drop_off_staff || "";
        });
    } else {
        modalTitle.textContent = "来訪日登録";
        submitButton.textContent = "登録";
        form.dataset.eventId = "";
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
    const form = document.getElementById("eventForm");

    form.reset();
    form.dataset.eventId = "";

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
    cancelButton.addEventListener("click", function () {
        closeModal();
    });
}
