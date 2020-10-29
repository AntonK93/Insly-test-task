Vue.component('second-test', {
    template: `
    <div class="card card-body mt-2">
        <h6 class="mt-2 mb-3">Test 2: Calculator</h6>
        <div class="d-flex justify-content-center">
            <div class="col-5">
                <label for="car-value">Estimated value of the car (100 - 100 000 EUR)</label>
                <div class="input-group mb-2">
                    <input id="car-value"
                           placeholder="Estimated value of the car"
                           type="number"
                           class="form-control"
                           @change="checkEstimatedValue()"
                           pattern="[0-9]+([\\.,][0-9]+)?"
                           step="0.01"
                           v-model="secondTestData.estimatedValue">
                    <div class="input-group-append">
                        <span class="input-group-text">EUR</span>
                    </div>
                 </div>

                <label for="tax-percentage">Tax percentage (0 - 100%)</label>
                <div class="input-group mb-2">
                    <input id="tax-percentage"
                           type="number"
                           placeholder="Tax percentage"
                           pattern="[0-9]+([\\.,][0-9]+)?"
                           @change="checkTaxAmount()"
                           step="0.01"
                           v-model="secondTestData.taxPercentage"
                           class="form-control">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <label for="instalments-number">Number of instalments (count of payments 1 – 12)</label>
                <input id="instalments-number"
                       placeholder="Number of instalments"
                       type="number"
                       @change="checkInstalments()"
                       v-model="secondTestData.instalmentsNumber"
                       class="form-control mb-2">
                <button class="btn btn-primary float-right mt-2"
                        :disabled="isCalculateButtonDisabled()"
                        @click="requestSecondTest">
                     Calculate
                </button>
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
                        {{ index }} instalment
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
    `,
    data: function () {
        return {
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
        }
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
        async requestSecondTest() {
            this.secondTestResult = await requestTest('task-2', this.secondTestData);
        },
    }
})
