import { createApp } from 'vue'
import SprintDemo from './components/SprintDemo.vue'
import ScheduledExportList from './components/ScheduledExportList.vue'

const app = createApp(SprintDemo)
app.component('scheduled-export-list', ScheduledExportList)
app.mount('#app')