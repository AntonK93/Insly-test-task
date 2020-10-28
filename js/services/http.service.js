function generateTestForm($type, fromObject = null) {
    const form = new FormData();
    for ( const key in fromObject ) {
        form.append(key, fromObject[key]);
    }
    form.append('type', $type);
    return form;
}

function requestTest(type, formObject = null) {
    return axios.post('requests/calculation.php', this.generateTestForm(type, formObject))
        .then((res) => {
            return res.data;
        });
}
