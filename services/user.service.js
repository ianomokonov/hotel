export class UserService {
  key = "olympicsUserInfo";
  tokenKey = "olympicsUserToken";
  redirectKey = "olympicsUserRedirect";
  user;
  token;
  defaultRedirect = '/profile';
  redirect = '/profile';
  constructor() {
    this.user = sessionStorage.getItem(this.key) ? JSON.parse(sessionStorage.getItem(this.key)) : null;
    this.token = sessionStorage.getItem(this.tokenKey) ? JSON.parse(sessionStorage.getItem(this.tokenKey)) : null;
    this.redirect = sessionStorage.getItem(this.redirectKey) ? JSON.parse(sessionStorage.getItem(this.redirectKey)) || this.defaultRedirect : null;
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
