export class ApiService {
  constructor() {
    this.baseUrl = "http://localhost/hotel/controller.php";
  }

  get(url) {
    return fetch(url).then((response) => {
      if (response.ok) {
        // если HTTP-статус в диапазоне 200-299
        // получаем тело ответа (см. про этот метод ниже)
        return response.json();
      } else {
        console.error("Ошибка HTTP: " + response.status);
      }
    });
  }

  post(url, data) {
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
      body: JSON.stringify(data),
    }).then((response) => {
      if (response.ok) {
        // если HTTP-статус в диапазоне 200-299
        // получаем тело ответа (см. про этот метод ниже)
        return response.json();
      } else {
        console.error("Ошибка HTTP: " + response.status);
      }
    });
  }

  signIn(email, password) {
    const url = `${this.base_url}key=sign-in`;
    return this.post(url, { email, password });
  }

  getRooms(fromDate = null, toDate = null) {
    const url = new URL(this.baseUrl);
    url.searchParams.set("key", "get-rooms");
    if (fromDate) {
      url.searchParams.set("dateFrom", this.dateToString(fromDate));
    }
    if (toDate) {
      url.searchParams.set("dateTo", this.ngbDateToString(toDate));
    }
    return this.get(url.href);
  }

  dateToString(date) {
    if (!date) {
      return date;
    }
    return `${
      date.getFullYear() < 10 ? `0${date.getFullYear()}` : date.getFullYear()
    }-${date.getMonth() < 10 ? `0${date.getMonth()}` : date.getMonth()}-${
      date.getDate() < 10 ? `0${date.getDate()}` : date.getDate()
    }`;
  }
}

export default ApiService;
