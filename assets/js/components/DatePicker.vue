<script>
import DatePicker from 'vue2-datepicker'
require('moment');

export default {
    components: { DatePicker },
    data() {
        return {
            time1: '',
            time2: '',
            time3: '',
            // custom lang
            lang: 'ru',
            // custom range shortcuts
            shortcuts: [
                {
                    text: 'Today',
                    onClick: () => {
                        this.time3 = [ new Date(), new Date() ]
                    }
                }
            ],
            timePickerOptions:{
                start: '00:00',
                step: '00:30',
                end: '23:30'
            }
        }
    },
    props: {
        value: {
            type: String,
            default: "0"
        }
    }
}
</script>

<template>
    <date-picker v-model="value" :lang="lang"></date-picker>

</template>