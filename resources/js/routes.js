import SamosvalComponent from "./components/SamosvalComponent.vue";
import SamosvalProblems from "./components/SamosvalProblems.vue";
import SamosvalSolutions from "./components/SamosvalSolutions.vue";
import SamosvalRequestComponent from "./components/SamosvalRequestComponent.vue";
import StaffStructureComponent from "./components/StaffStructureComponent.vue";
import InboxComponent from "./components/InboxComponent.vue";
import InboxHistoryComponent from "./components/InboxHistoryComponent.vue";
import LoginComponent from "./components/LoginComponent.vue";

export const routes = [
    { path: "/login", component: LoginComponent },
    // SamosvalS
    { path: "/Samosvals", component: SamosvalComponent },
    { path: "/Samosvals/problems", component: SamosvalProblems },
    { path: "/Samosvals/solutions", component: SamosvalSolutions },
    { path: "/Samosvals/requests", component: SamosvalRequestComponent },
    { path: "/inbox", component: InboxComponent },
    { path: "/inbox/:id/history", component: InboxHistoryComponent, props: true },

    // Staff / Structure
    { path: "/staff/employees", component: StaffStructureComponent, props: { section: "employees" } },
    { path: "/staff/departments", component: StaffStructureComponent, props: { section: "departments" } },
    { path: "/staff/contacts", component: StaffStructureComponent, props: { section: "contacts" } },
    { path: "/staff/roles", component: StaffStructureComponent, props: { section: "roles" } },
];
