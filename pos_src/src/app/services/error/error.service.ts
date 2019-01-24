import { Injectable } from '@angular/core';
import {Observable} from "rxjs/Observable";
import {of} from "rxjs/observable/of";
import {MessageService} from "../message/message.service";

@Injectable()
export class ErrorService {

  constructor(private messageService: MessageService) { }

    public log(msg: string = "") {
      console.log(msg);
        // this.messageService.add(`${msg}`);
    }

    public handleError<T>(operation = 'operation', result?: T) {
        return (err: any): Observable<T> => {

            // TODO: send the error to remote logging infrastructure
            console.error(err); // log to console instead

            // TODO: better job of transforming error for user consumption
            this.log(`${operation} failed: ${err.message}`);

            // Let the app keep running by returning an empty result.
            return of(result as T);
        };
    }

}
