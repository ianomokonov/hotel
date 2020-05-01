export class UserService {
  key = "hotelUserInfo";
  tokenKey = "hotelUserToken";
  redirectKey = "hotelUserRedirect";
  user;
  token;
  defaultRedirect = '/profile';
  redirect = '/profile';
  constructor() {
    this.user = JSON.parse(sessionStorage.getItem(this.key));
    this.token = JSON.parse(sessionStorage.getItem(this.tokenKey));
    this.redirect = JSON.parse(sessionStorage.getItem(this.redirectKey)) || this.defaultRedirect;
  }

  saveUser(user) {
    this.user = user;
    sessionStorage.setItem(this.key, JSON.stringify(user));
  }

  saveToken(token) {
    this.token = token;
    sessionStorage.setItem(this.tokenKey, JSON.stringify(token));
  }

  saveRedirect(redirect) {
    this.redirect = redirect || this.defaultRedirect;
    sessionStorage.setItem(this.redirectKey, JSON.stringify(this.redirect));
  }
}

export default UserService;
