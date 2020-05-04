import UserService from "./user.service.js";

export class ApiService {
  constructor() {
    this.baseUrl = "http://localhost/olympics/controller.php";
    this.url = "http://localhost/olympics/pages";
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

  addCourse(data) {
    const url = `${this.baseUrl}?key=add-course&token=${this.userService.token}`;
    return this.post(url, data);
  }

  updateCourse(data) {
    const url = `${this.baseUrl}?key=update-course&token=${this.userService.token}`;
    return this.post(url, data);
  }

  addQuestion(data) {
    const url = `${this.baseUrl}?key=add-question&token=${this.userService.token}`;
    return this.post(url, data);
  }

  updateQuestion(data) {
    const url = `${this.baseUrl}?key=update-question&token=${this.userService.token}`;
    return this.post(url, data);
  }

  saveAnswers(data) {
    const url = `${this.baseUrl}?key=save-answers&token=${this.userService.token}`;
    return this.post(url, data);
  }

  getCourse(id) {
    const url = `${this.baseUrl}?key=get-course&courseId=${id}`;
    return this.get(url);
  }

  getUserInfo() {
    const url = `${this.baseUrl}?key=get-user-info&token=${this.userService.token}`;
    return this.get(url);
  }

  checkUser() {
    const url = `${this.baseUrl}?key=check-admin&token=${this.userService.token}`;
    return this.get(url);
  }

  getCourses() {
    const url = `${this.baseUrl}?key=get-courses&token=${this.userService.token}`;
    return this.get(url);
  }
}

export default ApiService;
