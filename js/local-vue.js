const app = new Vue({
    el: '#myapp',
    data: {
        users: "",
        userid: 0
    },
    methods: {
        requestFirstTest()
        {
            axios.get('calculation.php?test=1')
                .then(function (response) {
                    app.users = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    }
}).$mount('#app');
