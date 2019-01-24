import {Http, Response} from '@angular/http';
import {Observable} from 'rxjs';
import {Injectable} from '@angular/core';

@Injectable()
export class IndexedDbService {
    private _indexedDB: any;
    private _dbName: string;
    private _dbVersion: number;

    constructor() {
        this._indexedDB = indexedDB;
        this._dbName = 'BookingDB'; // by default
        this._dbVersion = 1; // by default
    }

    setName(dbName: string): IndexedDbService {
        if (dbName.length > 0 && dbName !== undefined) {
            this._dbName = dbName;
        }
        else {
            console.log("Error: wrong dbName");
        }

        return this;
    }

    setVersion(dbVersion: number): IndexedDbService {
        if (dbVersion !== undefined) {
            this._dbVersion = dbVersion;
        }
        else {
            console.log("Error: wrong dbVersion");
        }

        return this;
    }

    put(source: string, object: any): Observable<any> {
        let self = this;

        return Observable.create((observer: any) => {
            this.open().subscribe((db: any) => {
                let tx = db.transaction(source, "readwrite");
                let store = tx.objectStore(source);
                store.put(object);

                tx.oncomplete = () => {
                    observer.next(object);
                    db.close();
                    observer.complete();
                };
                db.onerror = (e: any) => {
                    db.close();
                    self.handleError("IndexedDB error: " + e.target.errorCode);
                }
            });
        });
    };

    post(source: string, object: any): Observable<any> {
        let self = this;

        return Observable.create((observer: any) => {
            this.open().subscribe((db: any) => {
                let tx = db.transaction(source, "readwrite");
                let store = tx.objectStore(source);
                let request = store.add(object);

                request.onsuccess = (e: any) => {
                    observer.next(e.target.result);
                    db.close();
                    observer.complete();
                }
                db.onerror = (e: any) => {
                    db.close();
                    self.handleError("IndexedDB error: " + e.target.errorCode);
                }
            });
        });
    };

    get(source: string, id: number): Observable<any> {
        let self = this;

        return Observable.create((observer: any) => {
            this.open().subscribe((db: any) => {
                let tx = db.transaction(source, "readonly");
                let store = tx.objectStore(source);
                let index = store.index("id_idx");
                let request = index.get(id);

                request.onsuccess = () => {
                    observer.next(request.result);
                    db.close();
                    observer.complete();
                };
                db.onerror = (e: any) => {
                    db.close();
                    self.handleError("IndexedDB error: " + e.target.errorCode);
                }
            });
        });
    };

    all(source: string, indexName: string = "id_idx", direction: boolean = false, range: IDBKeyRange = null, pagingNumber: number = 0, page: number = 1): Observable<any[]> {
        let self = this;

        return Observable.create((observer: any) => {
            this.open().subscribe((db: any) => {
                let tx = db.transaction(source, "readonly");
                let store = tx.objectStore(source);
                let index = store.index(indexName);
                let request = index.openCursor(range, direction ? "next" : "prev");
                let results: any[] = [];
                let i: number = 1;
                let isPaging = pagingNumber ? true : false;

                request.onsuccess = function () {
                    let cursor = request.result;
                    if (cursor) {
                        if (isPaging && page > 1) {
                            cursor.advance(pagingNumber * (page - 1));
                            isPaging = false;
                        } else {
                            if (pagingNumber && i > pagingNumber) {
                                cursor = null;
                                observer.next(results);
                                db.close();
                                observer.complete();
                            } else {
                                results.push(cursor.value);
                                cursor.continue();
                                i++;
                            }
                        }
                    } else {
                        observer.next(results);
                        db.close();
                        observer.complete();
                    }
                };
                db.onerror = (e: any) => {
                    db.close();
                    self.handleError("IndexedDB error: " + e.target.errorCode);
                }
            });
        });
    };

    remove(source: string, id: number): Observable<any> {
        let self = this;

        return Observable.create((observer: any) => {
            this.open().subscribe((db: any) => {
                let tx = db.transaction(source, "readwrite");
                let store = tx.objectStore(source);

                store.delete(id);

                tx.oncomplete = (e: any) => {
                    observer.next(id);
                    db.close();
                    observer.complete();
                };
                db.onerror = (e: any) => {
                    db.close();
                    self.handleError("IndexedDB error: " + e.target.errorCode);
                }
            });
        });
    };

    count(source: string, indexName: string = 'id_idx', keyRange?: IDBKeyRange): Observable<number> {
        let self = this;

        return Observable.create((observer: any) => {
            this.open().subscribe((db: any) => {
                let tx = db.transaction(source, "readonly");
                let store = tx.objectStore(source);
                let index = store.index(indexName);
                let request = index.count(keyRange);

                request.onsuccess = () => {
                    observer.next(request.result);
                    db.close();
                    observer.complete();
                };
                db.onerror = (e: any) => {
                    db.close();
                    self.handleError("IndexedDB error: " + e.target.errorCode);
                }
            });
        });
    };

    create(schema?: any[]): Observable<any> {
        let self = this;

        return Observable.create((observer: any) => {
            let request = this._indexedDB.open(this._dbName, this._dbVersion);

            request.onupgradeneeded = (e) => {
                // The database did not previously exist, so create object stores and indexes.
                let db = request.result;

                for (let i = 0; i < schema.length; i++) {
                    let store = db.createObjectStore(schema[i].name, {keyPath: "id", autoIncrement: true});
                    store.createIndex("id_idx", "id", {unique: true});

                    if (schema[i].indexes !== undefined) {
                        for (let j = 0; j < schema[i].indexes.length; j++) {
                            let index = schema[i].indexes[j];
                            store.createIndex(`${index}_idx`, index);
                        }
                    }

                    if (schema[i].seeds !== undefined) {
                        for (let j = 0; j < schema[i].seeds.length; j++) {
                            let seed = schema[i].seeds[j];
                            store.put(seed);
                        }
                    }
                }

                observer.next('done');
                observer.complete();
            };

            request.onerror = () => {
                self.handleError(request.error);
            }

            request.onsuccess = () => {
                let db = request.result;
                db.close();
            }
        });
    }

    clear(): Observable<any> {
        let self = this;

        return Observable.create((observer: any) => {
            let request = this._indexedDB.deleteDatabase(this._dbName);

            request.onsuccess = () => {
                observer.next('done');
                observer.complete();
            }
            request.onerror = () => {
                self.handleError('Could not delete indexed db.');
            };
            request.onblocked = () => {
                self.handleError('Couldn not delete database due to the operation being blocked.');
            };
        });
    }

    private handleError(msg: string) {
        console.error(msg);
        return Observable.throw(msg);
    }

    private open(): Observable<any> {
        let self = this;
        return Observable.create((observer: any) => {
            let request = this._indexedDB.open(this._dbName, this._dbVersion);

            request.onsuccess = () => {
                observer.next(request.result);
                observer.complete();
            }
            request.onerror = () => self.handleError(request.error);
        });
    }

    init(): Observable<any> {
        let self = this;
        return Observable.create((observer: any) => {
            let request: IDBOpenDBRequest = this._indexedDB.open(this._dbName, this._dbVersion);
            request.onupgradeneeded = (e: IDBVersionChangeEvent) => {

                console.log("Initial indexedDB");

                // The database did not previously exist, so create object stores and indexes.
                let db = request.result;

                if (e.oldVersion < 1) {
                    this.initV1(db);
                }

                observer.next('done');
                observer.complete();
            };

            request.onerror = (e) => {
                return self.handleError("Err: init IndexedDB");
            }

            request.onsuccess = () => {
                let db = request.result;
                db.close();
            }
        });
    }

    initV1(db) {
        let storeProduct = db.createObjectStore('products', {keyPath: "id", autoIncrement: false});
        storeProduct.createIndex("id_idx", "id", {unique: true});
        storeProduct.createIndex("price_idx", "price", {unique: false});
        storeProduct.createIndex("group_idx", "group", {unique: false});
        storeProduct.createIndex("type_idx", "type", {unique: false});


        let city = db.createObjectStore('cities', {keyPath: "id", autoIncrement: false});
        city.createIndex("id_idx", "id", {unique: true});
        city.createIndex("name_idx", "name", {unique: false});

        let store = db.createObjectStore('stores', {keyPath: "id", autoIncrement: false});
        store.createIndex("id_idx", "id", {unique: true});
        store.createIndex("name_idx", "name", {unique: false});
        store.createIndex("cityId_idx", "cityId", {unique: false});

        let storeStaff = db.createObjectStore('staffs', {keyPath: "id", autoIncrement: false});
        storeStaff.createIndex("id_idx", "id", {unique: true});

        let workScheduleStaff = db.createObjectStore('work_schedules', {keyPath: "id", autoIncrement: false});
        workScheduleStaff.createIndex("id_idx", "id", {unique: true});
        workScheduleStaff.createIndex("swId_idx", "swId", {unique: false});
        workScheduleStaff.createIndex("staffId_idx", "staffId", {unique: false});
        workScheduleStaff.createIndex("storeId_idx", "storeId", {unique: false});
        workScheduleStaff.createIndex("date_idx", "date", {unique: false});

        let shiftWorkStaff = db.createObjectStore('shift_works', {keyPath: "id", autoIncrement: false});
        shiftWorkStaff.createIndex("id_idx", "id", {unique: true});
    }
}