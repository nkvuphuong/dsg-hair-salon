import { TestBed, inject } from '@angular/core/testing';

import { ShiftWorkService } from './shift-work.service';

describe('ShiftWorkService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ShiftWorkService]
    });
  });

  it('should be created', inject([ShiftWorkService], (service: ShiftWorkService) => {
    expect(service).toBeTruthy();
  }));
});
