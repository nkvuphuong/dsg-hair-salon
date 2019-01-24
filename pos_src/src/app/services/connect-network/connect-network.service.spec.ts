import { TestBed, inject } from '@angular/core/testing';

import { ConnectNetworkService } from './connect-network.service';

describe('ConnectNetworkService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ConnectNetworkService]
    });
  });

  it('should be created', inject([ConnectNetworkService], (service: ConnectNetworkService) => {
    expect(service).toBeTruthy();
  }));
});
