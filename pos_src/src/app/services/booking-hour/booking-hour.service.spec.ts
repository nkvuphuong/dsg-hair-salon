import { TestBed, inject } from '@angular/core/testing';

import { BookingHourService } from './booking-hour.service';

describe('BookingHourService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [BookingHourService]
    });
  });

  it('should be created', inject([BookingHourService], (service: BookingHourService) => {
    expect(service).toBeTruthy();
  }));
});
