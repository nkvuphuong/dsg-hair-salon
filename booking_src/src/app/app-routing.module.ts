import {NgModule} from '@angular/core';
import {RouterModule, Routes} from "@angular/router";
import {StoreComponent} from "./components/store/store.component";
import {HomeComponent} from "./components/home/home.component";
import {BookComponent} from "./components/book/book.component";
import {CompleteComponent} from "./components/complete/complete.component";
import {InformationComponent} from "./components/information/information.component";
import {AuthGuard} from "./components/auth/auth.guard";

const routes: Routes = [
    {path: '', component: HomeComponent},
    {path: 'store', component: StoreComponent},
    {path: 'book', component: BookComponent},
    {path: 'complete', component: CompleteComponent},
    {path: 'complete/:phone/:order', component: CompleteComponent},
    {path: 'information/:phone', component: InformationComponent, canActivate: [AuthGuard]}
];

@NgModule({
    exports: [RouterModule],
    imports: [
        RouterModule.forRoot(routes)
    ]
})

export class AppRoutingModule {
}
