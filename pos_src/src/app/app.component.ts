import {Component} from '@angular/core';
import {SwUpdate} from "@angular/service-worker";
import {IndexedDbService} from "./services/indexed-db/indexed-db.service";
import {isOnline} from "./lib/script";

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: [
        './app.component.css',
    ],
})
export class AppComponent {
    title = 'POS project';
    databaseCreated: boolean;

    constructor(private swUpdate: SwUpdate, private idbService: IndexedDbService) {
    }

    ngOnInit() {

        this.idbService.setName('POSDB').setVersion(1).init().subscribe();

        //Check new versions
        /*if (this.swUpdate.isEnabled) {

            this.swUpdate.available.subscribe(() => {

                if(confirm("New version available. Load New Version?")) {

                    window.location.reload();
                }
            });
        }*/
    }
}
