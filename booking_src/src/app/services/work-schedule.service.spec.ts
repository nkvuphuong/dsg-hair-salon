import { TestBed, inject } from '@angular/core/testing';

import { WorkScheduleService } from './work-schedule.service';

describe('WorkScheduleService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [WorkScheduleService]
    });
  });

  it('should be created', inject([WorkScheduleService], (service: WorkScheduleService) => {
    expect(service).toBeTruthy();
  }));
});
