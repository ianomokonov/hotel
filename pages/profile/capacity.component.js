export class CapacityComponent {
  element;
  subElements = {};
  dates = [];
  rooms;
  constructor(rooms = []) {
      this.rooms = rooms;
    this.setDates(new Date());
    this.render();
    this.initEventListeners();
    
  }

  get tableHead() {
      return `
      <td>Номер</td>
      ${this.dates.map(d => 
        `<td>${d.toLocaleDateString()}</td>`
      ).join('')}
      `;
  }

  get tableBody() {
      return `
      ${this.rooms.map(room => 
        `<tr><td>${room.id}</td>${this.dates.map(d => 
            `<td class="${room.dates.find(range => {
                let dateFrom = new Date(range.dateFrom);
                if(!range.dateTo){
                    return dateFrom.toLocaleDateString() === d.toLocaleDateString(); 
                }
                let dateTo = new Date(range.dateTo);
                return dateFrom.getTime() <= d.getTime() && dateTo.getTime() >= d.getTime(); 
            }) ? 'booked' : ''}"></td>`
          ).join('')}
        </tr>`
      ).join('')}
      `;
  }

  setDates(date) {
    let weekDay = date.getDay();
    let d = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - weekDay + 1
    );
    
    this.dates = [new Date(d)];
    d.setDate(d.getDate() + 1);
    while(d.getDay() !== 1){
        this.dates.push(new Date(d));
        d.setDate(d.getDate() + 1);
    }
  }

  render() {
    let elem = document.createElement("div");
    elem.innerHTML = `
    <div>
        <div class="btns my-3">
            <button class="prev"><i class="fas fa-arrow-left"></i></button>
            <button class="next"><i class="fas fa-arrow-right"></i></button>
        </div>
        <table>
            <thead >
                ${this.tableHead}
            </thead>
            <tbody>
                ${this.tableBody}
            </tbody>
        </table>
    </div>
    `;
    this.element = elem.firstElementChild;
    this.subElements.thead = this.element.querySelector("thead");
    this.subElements.tbody = this.element.querySelector("tbody");
  }

  onBtnClick = ({target}) => {
    if(target.closest('.prev')){
        let d = new Date(this.dates[0]);
        d.setDate(d.getDate() - 7);
        this.setDates(d);
        this.subElements.thead.innerHTML = this.tableHead;
        this.subElements.tbody.innerHTML = this.tableBody;
    }

    if(target.closest('.next')){
        let d = new Date(this.dates[0]);
        d.setDate(d.getDate() + 7);
        this.setDates(d);
        this.subElements.thead.innerHTML = this.tableHead;
        this.subElements.tbody.innerHTML = this.tableBody;
    }

  }

  initEventListeners() {
      this.element.querySelector('.btns').addEventListener('click', this.onBtnClick);
  }
}

export default CapacityComponent;
