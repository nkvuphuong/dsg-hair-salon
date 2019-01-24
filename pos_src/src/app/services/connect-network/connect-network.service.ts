import {Injectable} from '@angular/core';
import {Observable} from "rxjs/Observable";
import 'rxjs/Rx';

@Injectable()
export class ConnectNetworkService {

    public isConnected: Observable<boolean>;

    constructor() {
        this.isConnected = Observable.merge(
            Observable.of(navigator.onLine),
            Observable.fromEvent(window, 'online').map((e) => true),
            Observable.fromEvent(window, 'offline').map((e) => false));
    }
}
