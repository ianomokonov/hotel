import UserService from './user.service.js';

export class ApiService {
  constructor() {
    this.baseUrl = "http://localhost/hotel/controller.php";
    this.url = "http://localhost/hotel/pages";
    this.userService = new UserService();
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
    const url = `${this.baseUrl}?key=sign-in`;
    return this.post(url, { email, password });
  }

  signUp(data) {
    const url = `${this.baseUrl}?key=sign-up`;
    return this.post(url, data);
  }

  saveUserInfo(data) {
    const url = `${this.baseUrl}?key=update-user-info&token=${this.userService.token}`;
    return this.post(url, data);
  }

  addOrder(data) {
    const url = `${this.baseUrl}?key=add-order&token=${this.userService.token}`;
    return this.post(url, data);
  }

  cancelOrder(id) {
    const url = `${this.baseUrl}?key=cancel-order&orderId=${id}&token=${this.userService.token}`;
    return this.get(url);
  }

  getUserInfo() {
    const url = `${this.baseUrl}?key=get-user-info&token=${this.userService.token}`;
    return this.get(url);
  }

  getHistory() {
    const url = `${this.baseUrl}?key=get-history&token=${this.userService.token}`;
    return this.get(url);
  }

  getRooms(filters) {
    const url = new URL(this.baseUrl);
    url.searchParams.set("key", "get-rooms");
    if (filters.fromDate) {
      url.searchParams.set("dateFrom", filters.fromDate);
    }
    if (filters.toDate && filters.toDate != filters.fromDate) {
      url.searchParams.set("dateTo", filters.toDate);
    }
    if (filters.count) {
      url.searchParams.set("count", filters.count);
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
