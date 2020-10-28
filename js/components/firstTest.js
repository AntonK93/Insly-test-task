Vue.component('first-test', {
    template: `
        <div class="card card-body">
            <h6 class="mt-2 mb-3">Test 1: print out your name with one of php loops</h6>
            <div class="d-flex justify-content-center">
                <button class="btn btn-primary mt-2" @click="requestFirstTest">Get result</button>
            </div>
            <h6 v-if="firstTestResult">
                Result is: {{ firstTestResult }}<br>
                <em>(the logic takes place under getFirstTestResult in calculation.php)</em>
            </h6>
        </div>
    `,
    data: function () {
        return {
            firstTestResult: '',
        }
    },
    methods: {
        async requestFirstTest() {
            this.firstTestResult = await requestTest('task-1');
        },
    }
})
