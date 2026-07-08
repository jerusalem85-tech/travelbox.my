import { IsString, IsOptional, IsNumber, IsDateString, IsUUID, IsInt } from 'class-validator';

export class CreateTransferDto {
  @IsUUID()
  tripId: string;

  @IsString()
  type: string;

  @IsString()
  pickupLocation: string;

  @IsString()
  dropoffLocation: string;

  @IsDateString()
  pickupDate: string;

  @IsOptional()
  @IsString()
  pickupTime?: string;

  @IsOptional()
  @IsDateString()
  dropoffDate?: string;

  @IsString()
  vehicleType: string;

  @IsInt()
  passengers: number;

  @IsOptional()
  @IsString()
  flightNo?: string;

  @IsOptional()
  @IsString()
  confirmationRef?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsNumber()
  costPrice?: number;

  @IsOptional()
  @IsNumber()
  sellPrice?: number;

  @IsOptional()
  @IsUUID()
  supplierId?: string;

  @IsOptional()
  @IsString()
  supplierRef?: string;

  @IsOptional()
  @IsString()
  notes?: string;
}

export class UpdateTransferDto {
  @IsOptional() @IsString() type?: string;
  @IsOptional() @IsString() pickupLocation?: string;
  @IsOptional() @IsString() dropoffLocation?: string;
  @IsOptional() @IsDateString() pickupDate?: string;
  @IsOptional() @IsString() pickupTime?: string;
  @IsOptional() @IsDateString() dropoffDate?: string;
  @IsOptional() @IsString() vehicleType?: string;
  @IsOptional() @IsInt() passengers?: number;
  @IsOptional() @IsString() flightNo?: string;
  @IsOptional() @IsString() confirmationRef?: string;
  @IsOptional() @IsString() status?: string;
  @IsOptional() @IsNumber() costPrice?: number;
  @IsOptional() @IsNumber() sellPrice?: number;
  @IsOptional() @IsUUID() supplierId?: string;
  @IsOptional() @IsString() supplierRef?: string;
  @IsOptional() @IsString() notes?: string;
}
