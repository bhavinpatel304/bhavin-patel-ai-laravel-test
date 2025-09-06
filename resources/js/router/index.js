import { createRouter, createWebHistory } from "vue-router";

import TicketList from "../components/ticket/TicketList.vue";
import TicketDetail from "../components/ticket/TicketDetail.vue";
import Dashboard from "../components/dashboard/Dashboard.vue";

const routes = [
    { path: '/', component: Dashboard },
    { path: '/tickets', component: TicketList },
    { path: '/tickets/:id', component: TicketDetail, props: true },    
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
