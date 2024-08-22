function ctpTableController(step=10) {
    let data = [ ...window.CTP.users.sort((a,b) => a.firstName > b.firstName) ];
    let slice = data.slice(0, step);
    let tbodyRef = document.getElementById(window.CTP.tableId).getElementsByTagName('tbody')[0];

    slice.map((user) => {
        let row = tbodyRef.insertRow();

        for (let prop in user) {
            row.insertCell().appendChild(document.createTextNode(user[prop]));
        }
    });

    window.CTP.users = data.slice(step++);

    if (window.CTP.users.length === 0) {
        let btn = document.getElementById(window.CTP.contorllerId);

        if (btn) {
            btn.style.display = 'none';
        }
    }
}
