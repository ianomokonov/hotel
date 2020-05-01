export class SearchService {
  key = "filtersFormValue";
  selectedRoomKey = "selectedRoom";
  filters;
  room;
  constructor() {
    this.filters = JSON.parse(sessionStorage.getItem(this.key)) || {};
    this.room = JSON.parse(sessionStorage.getItem(this.selectedRoomKey));
  }

  saveFilters(filters){
    this.filters = filters || {};
    sessionStorage.setItem(this.key, JSON.stringify(filters));
  }

  saveRoom(room){
    this.room = room;
    sessionStorage.setItem(this.selectedRoomKey, JSON.stringify(room));
  }

  getPrice(filters, price) {
    price = +price;
    if (filters.cancel) {
      price *= 1.1;
    }
    if (filters.breakfast) {
      price *= 1.2;
    }
    return Math.round(price);
  }

  getPeriod(dateStart, dateFinish) {
    return Math.round((dateFinish.getTime() - dateStart.getTime()) / (24 * 3600000)) + 1;
  }
}

export default SearchService;
