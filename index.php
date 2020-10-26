<!DOCTYPE html>
<html>
<head>
    <title>Test app</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- vue -->
    <script src="https://unpkg.com/vue@2.6.12/dist/vue.js"></script>
    <!-- axios -->
    <script src="https://unpkg.com/axios@0.21.0/dist/axios.min.js"></script>
</head>
<body>
<div id="app" class="container-fluid col-11 mt-2 mb-2">
    <div class="card card-body">
        <h6 class="mt-2 mb-3">Test 1: print out your name with one of php loops</h6>
        <div class="d-flex justify-content-center">
            <form v-on:submit.prevent="requestFirstTest">
                <button class="btn btn-primary mt-2">Get result</button>
            </form>
        </div>
        <h6 v-if="firstTestResult">
            Result is: {{ firstTestResult }}<br>
            <em>(the logic takes place under getFirstTestResult in calculation.php)</em>
        </h6>
    </div>
    <div class="card card-body mt-2">
        <h6 class="mt-2 mb-3">Test 2: Calculator</h6>
        <div class="d-flex justify-content-center">
            <div class="col-5">
                <form v-on:submit.prevent="requestSecondTest">
                    <label for="car-value">Estimated value of the car (100 - 100 000 EUR)</label>
                    <div class="input-group mb-2">
                        <input id="car-value"
                               placeholder="Estimated value of the car"
                               type="number"
                               class="form-control"
                               @change="checkEstimatedValue()"
                               pattern="[0-9]+([\.,][0-9]+)?"
                               step="0.01"
                               v-model="secondTestData.estimatedValue">
                        <div class="input-group-append">
                            <span class="input-group-text">EUR</span>
                        </div>
                    </div>

                    <label for="tax-percentage">Tax percentage (0 - 100%)</label>
                    <div class="input-group mb-2">
                        <input
                            id="tax-percentage"
                            type="number"
                            placeholder="Tax percentage"
                            pattern="[0-9]+([\.,][0-9]+)?"
                            @change="checkTaxAmount()"
                            step="0.01"
                            v-model="secondTestData.taxPercentage"
                            class="form-control">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <label for="instalments-number">Number of instalments (count of payments 1 – 12)</label>
                    <input
                        id="instalments-number"
                        placeholder="Number of instalments"
                        type="number"
                        @change="checkInstalments()"
                        v-model="secondTestData.instalmentsNumber"
                        class="form-control mb-2">
                    <button class="btn btn-primary float-right mt-2" :disabled="isCalculateButtonDisabled()">
                        Calculate
                    </button>
                </form>
            </div>
        </div>

        <h6 v-if="secondTestResult.message">
            <em>Result: {{ secondTestResult.message }}</em>
        </h6>

        <div v-if="secondTestResult.policy" class="mt-4">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Policy</th>
                    <th scope="col" v-for="(instalment, index) in secondTestResult.instalments">
                        {{ `${index} instalment` }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Value</td>
                    <td>{{ filterValue(secondTestResult.policy.value) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Base premium ({{ secondTestResult.policy.basePercent }}%)</th>
                    <td>{{ filterValue(secondTestResult.policy.basePrice) }}</td>
                    <td v-for="instalment in secondTestResult.instalments">{{ filterValue(instalment.basePrice) }}</td>
                </tr>
                <tr>
                    <td>Commission ({{ secondTestResult.policy.commissionPercent }}%)</th>
                    <td>{{ filterValue(secondTestResult.policy.commission) }}</td>
                    <td v-for="instalment in secondTestResult.instalments">{{ filterValue(instalment.commission) }}</td>
                </tr>
                <tr>
                    <td>Tax ({{ secondTestResult.policy.taxPercent }}%)</th>
                    <td>{{ filterValue(secondTestResult.policy.tax) }}</td>
                    <td v-for="instalment in secondTestResult.instalments">{{ filterValue(instalment.tax) }}</td>
                </tr>
                <tr>
                    <th>Total cost</th>
                    <th>{{ filterValue(secondTestResult.policy.totalCost) }}</th>
                    <td v-for="instalment in secondTestResult.instalments">{{ filterValue(instalment.totalCost) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card card-body mt-2">
        <h6 class="mt-2 mb-3">Test 3: Store employee data</h6>
        <div class="d-flex justify-content-center">
            <em>Sql file 'db.sql' for test takes place under root project folder</em>
        </div>
    </div>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                firstTestResult: '',
                secondTestData: {
                    estimatedValue: null,
                    taxPercentage: null,
                    instalmentsNumber: null,
                },
                secondTestResult: {
                    policy: null,
                    instalments: null,
                    message: null,
                }
            };
        },
        methods: {
            isCalculateButtonDisabled() {
                return this.secondTestData.instalmentsNumber === null ||
                    this.secondTestData.taxPercentage === null ||
                    this.secondTestData.estimatedValue === null
            },
            filterValue(value) {
                return value.toFixed(2);
            },
            setBetweenRange(value, from,to) {
                if (value < from) {
                    return from;
                }
                else if (value > to) {
                    return to;
                }
                return value;
            },
            checkEstimatedValue() {
                this.secondTestData.estimatedValue =
                    this.setBetweenRange(this.secondTestData.estimatedValue, 100, 100000);
            },
            checkTaxAmount() {
                this.secondTestData.taxPercentage =
                    this.setBetweenRange(this.secondTestData.taxPercentage, 0, 100);
            },
            checkInstalments() {
                this.secondTestData.instalmentsNumber =
                    this.setBetweenRange(this.secondTestData.instalmentsNumber, 1, 12);
            },
            generateTestForm($type, fromObject = null) {
                const form = new FormData();
                for ( const key in fromObject ) {
                    form.append(key, fromObject[key]);
                }
                form.append('type', $type);
                return form;
            },
            requestFirstTest() {
                axios.post('requests/calculation.php', this.generateTestForm('task-1'))
                    .then((res) => {
                        this.firstTestResult = res.data;
                    });
            },
            requestSecondTest() {
                axios.post('requests/calculation.php', this.generateTestForm('task-2', this.secondTestData))
                    .then((res) => {
                        this.secondTestResult = res.data;
                    });
            },
        }
    });
</script>
</body>
</html>
